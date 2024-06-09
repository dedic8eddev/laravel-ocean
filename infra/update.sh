#!/usr/bin/env bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

PROJECT_NAME='orca'
PROJECT_SUFFIX='ocean-api'

AWS_REGION=eu-west-1
TASK_DEF_TEMPLATE_FILE=template.yml

RESOURCE_REASON=$1
RESOURCE_TYPE=$2
STACK_ENV=$3

if [ "${RESOURCE_REASON}" != "worker" ] && [ "${RESOURCE_REASON}" != "web" ]; then
	echo -e "Resource reason can only be one of: web | worker"
	exit 0
fi

if [ "${RESOURCE_TYPE}" != "service" ] && [ "${RESOURCE_TYPE}" != "service-check" ] && [ "${RESOURCE_TYPE}" != "task-definition" ]; then
	echo -e "Resource types can only be one of: task-definition | service | service-check"
	exit 0
fi

if [ "${STACK_ENV}" != "stage" ] && [ "${STACK_ENV}" != "prod" ] && [ "${STACK_ENV}" != "dev" ]; then
	echo -e "Environment can only be one of: dev | stage | prod"
	exit 0
fi

STACK_PREFIX="$PROJECT_NAME-$STACK_ENV-$PROJECT_SUFFIX"
CLUSTER_PREFIX="$PROJECT_NAME-$STACK_ENV"

if [[ "${RESOURCE_TYPE}" == "task-definition" ]]; then

	set +e
	update_output=$( aws cloudformation update-stack \
	--region ${AWS_REGION} \
	--stack-name ${STACK_PREFIX}-task-definition-${RESOURCE_REASON} \
	--template-body file://$SCRIPT_DIR/${TASK_DEF_TEMPLATE_FILE} \
	--parameters \
		ParameterKey=ProjectSuffix,ParameterValue=${PROJECT_SUFFIX} \
		ParameterKey=TaskReason,ParameterValue=${RESOURCE_REASON} \
	 	ParameterKey=Environment,ParameterValue=${STACK_ENV} \
	2>&1)
	status=$?
	set -e

	echo "$update_output"

	if [ $status -ne 0 ] ; then
		# Don't fail for no-op update
		if [[ $update_output == *"ValidationError"* && $update_output == *"No updates"* ]] ; then
			echo -e "\nFinished create/update - no updates to be performed"
			exit 0
		else
			exit $status
		fi
	fi

	echo "Waiting for the update to complete..."
	aws cloudformation wait stack-update-complete \
	--region eu-west-1 \
	--stack-name ${STACK_PREFIX}-task-definition-${RESOURCE_REASON}

	echo "Update of task definition has finished."

elif [[ "${RESOURCE_TYPE}" == "service" ]]; then

	TASK_DEF_ARN=$( aws cloudformation describe-stacks \
	--region ${AWS_REGION} \
	--stack-name ${STACK_PREFIX}-task-definition-${RESOURCE_REASON} \
	--query "Stacks[0].Outputs[?OutputKey=='TaskDefinition'].OutputValue" \
	--output text)

	aws ecs update-service \
	--region ${AWS_REGION} \
	--cluster ${CLUSTER_PREFIX} \
	--service ${STACK_PREFIX}-${RESOURCE_REASON} \
	--task-definition $TASK_DEF_ARN  \
	--force-new-deployment

elif [[ "${RESOURCE_TYPE}" == "service-check" ]]; then

	aws ecs wait services-stable \
	--region ${AWS_REGION} \
	--cluster ${CLUSTER_PREFIX} \
	--service ${STACK_PREFIX}-${RESOURCE_REASON}

	RETVAL=$?

	if [[ $RETVAL -eq 0 ]]; then
	   echo "Service is up and running"
	   exit 0
	else
	   echo "Problem - timed out waiting for ${ECS_SERVICE} to stabilize."
	   exit $RETVAL
	fi
fi
