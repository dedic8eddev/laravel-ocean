def AWS_ENV_TAG = ''
def SERVER_FOR_CRONS = ''
def COMMAND_FOR_CRONS = ''
def PROJECT = 'orca'
def REPO_SUFFIX = 'ocean-api'

pipeline {
    agent any
    parameters {
        gitParameter(name: 'BRANCH', branchFilter: 'origin/(.*)', defaultValue: 'dev', type: 'PT_BRANCH', description: 'Select branch to build', sortMode: 'ASCENDING_SMART')
        choice(name: 'ENV', choices: ['STAGE','PRODUCTION'], description: 'Choose a deployment environment')
        string(name: 'APP_VERSION', description: 'Choose an App Version (Git Tag) to tag the Image on the registry')
        booleanParam(name: 'RUN_TESTS',defaultValue: true, description: 'Whether to run the tests')
        booleanParam(name: 'DEPLOY_EC2',defaultValue: true, description: 'Deploy dedicated crons EC2')
        booleanParam(name: 'DEPLOY_ECS_WEB',defaultValue: false, description: 'Deploy web ECS services')
        booleanParam(name: 'DEPLOY_ECS_WORKER',defaultValue: false, description: 'Deploy worker ECS services')
    }
    stages {
        stage('Initialize Variables') {
            steps{
                script {
                    echo "Initializing env variables"
                    if (params.ENV == 'STAGE') {
                        AWS_ENV_TAG = 'stage'
                        SERVER_FOR_CRONS = 'cross-project'
                        COMMAND_FOR_CRONS = "cd tanker && ./init.sh ${PROJECT}-${REPO_SUFFIX} deploy"
                    } else if (params.ENV == 'PRODUCTION') {
                        AWS_ENV_TAG = 'prod'
                        SERVER_FOR_CRONS = 'orca'
                        COMMAND_FOR_CRONS = "cd tanker && ./init.sh ocean deploy"
                    }
                    echo "Initialized AWS_ENV_TAG to ${AWS_ENV_TAG}"
                }
            }
        }
        stage("Env") {
            parallel {
                stage('Stage') {
                    when{
                        expression {params.ENV == 'STAGE'}
                    }
                    steps {
                        echo "Starting Stage routines"
                    }
                }
                stage('Production') {
                    when{
                        expression {params.ENV == 'PRODUCTION'}
                    }
                    steps {
                        echo "Starting Production routines"
                    }
                }
            }
        }
        stage('Checkout'){
            steps {
                echo "Checking out branch ${params.BRANCH}"
                git branch: "${params.BRANCH}", url: "${env.GIT_URL}", credentialsId: 'gitlab-key'
            }
        }
        stage('Build Docker Image') {
            steps{
                script{
                    docker.build "${PROJECT}/${REPO_SUFFIX}:latest"
                }
            }
        }
        stage('Run Tests') {
            when{
                expression {return params.RUN_TESTS}
            }
            stages {
                stage('Update web-dev task-definition') {
                    steps{
                        withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                            sh """
                                export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                                export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
                            """
                            echo 'Updating dev task definition to run tests with.'
                            sh "./infra/update.sh web task-definition dev"
                        }
                    }
                }
                stage('Create test env from task-definition') {
                    steps {
                        withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                            sh """
                                export AWS_DEFAULT_REGION=eu-west-1
                                export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                                export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}

                                /awsenv/aws.sh arn:aws:ecs:eu-west-1:742659675016:task-definition/${PROJECT}-dev-${REPO_SUFFIX}-web test-env-vars
                            """
                       }
                    }
                }
                stage('Run tests') {
                    steps{
                        script{
                            sh "./scripts/jenkins/tests-container.sh up"
                            sh "./scripts/jenkins/tests-container.sh exec"
                        }
                    }
                    post {
                        always {
                            sh "./scripts/jenkins/tests-container.sh down"
                        }
                    }
                }
            }
        }
        stage('Push to ECR') {
            steps{
                withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                    sh """
                        export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                        export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
                    """
                    echo "Uploading image to ECR with tags ${params.APP_VERSION} and ${AWS_ENV_TAG}"
                    sh '''
                        $(aws ecr get-login --no-include-email --region eu-west-1)
                    '''
                    sh """
                        docker tag ${PROJECT}/${REPO_SUFFIX}:latest 742659675016.dkr.ecr.eu-west-1.amazonaws.com/${PROJECT}/${REPO_SUFFIX}:${AWS_ENV_TAG}
                        docker tag ${PROJECT}/${REPO_SUFFIX}:latest 742659675016.dkr.ecr.eu-west-1.amazonaws.com/${PROJECT}/${REPO_SUFFIX}:${params.APP_VERSION}
                        docker push 742659675016.dkr.ecr.eu-west-1.amazonaws.com/${PROJECT}/${REPO_SUFFIX}:${AWS_ENV_TAG}
                        docker push 742659675016.dkr.ecr.eu-west-1.amazonaws.com/${PROJECT}/${REPO_SUFFIX}:${params.APP_VERSION}
                    """
               }
            }
        }
        stage("Task Definitions") {
            parallel {
                stage('Web') {
                    steps{
                        withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                            sh """
                                export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                                export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
                            """
                            echo 'Updating Web API ECS Service.'
                            sh "./infra/update.sh web task-definition ${AWS_ENV_TAG}"
                        }
                    }
                }
                stage('Worker') {
                    steps{
                        withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                            sh """
                                export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                                export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
                            """
                            echo 'Updating Web API ECS Service.'
                            sh "./infra/update.sh worker task-definition ${AWS_ENV_TAG}"
                        }
                    }
                }
            }
        }
        stage("Deploy") {
            parallel {
                stage('EC2 crons') {
                    when{
                        expression {return params.DEPLOY_EC2}
                    }
                    // make this to fail the build if the ssh command returns error
                    steps {
                        sshPublisher(
                            failOnError: true,
                            publishers: [
                                sshPublisherDesc(
                                    configName: "${SERVER_FOR_CRONS}",
                                    transfers: [
                                        sshTransfer(
                                            execCommand: "${COMMAND_FOR_CRONS}"
                                        )
                                    ]
                                )
                            ]
                        )
                    }
                }
                stage("Web Service") {
                    when{
                        expression {return params.DEPLOY_ECS_WEB}
                    }
                    stages {
                        stage('Update Web ECS Services') {
                            steps{
                                withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                                    sh """
                                        export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                                        export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
                                    """
                                    echo 'Updating Web API ECS Service.'
                                    sh "./infra/update.sh web service ${AWS_ENV_TAG}"
                                }
                            }
                        }
                        // stage('Wait ECS Services to Deploy') {
                        //     steps{
                        //         withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                        //             sh """
                        //                 export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                        //                 export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
                        //             """
                        //             echo 'Waiting Web API ECS Service Deployment to finish.'
                        //             sh "./infra/update.sh web service-check ${AWS_ENV_TAG}"
                        //         }
                        //     }
                        // }
                    }
                }
                stage("Worker Service") {
                    when{
                        expression {return params.DEPLOY_ECS_WORKER}
                    }
                    stages {
                        stage('Update Worker ECS Services') {
                            steps{
                                withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                                    sh """
                                        export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                                        export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
                                    """
                                    echo 'Updating Web API ECS Service.'
                                    sh "./infra/update.sh worker service ${AWS_ENV_TAG}"
                                }
                            }
                        }
                        // stage('Wait ECS Services to Deploy') {
                        //     steps{
                        //         withCredentials([[$class: 'AmazonWebServicesCredentialsBinding',credentialsId: 'iam_tatoi']]) {
                        //             sh """
                        //                 export AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
                        //                 export AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
                        //             """
                        //             echo 'Waiting Web API ECS Service Deployment to finish.'
                        //             sh "./infra/update.sh worker service-check ${AWS_ENV_TAG}"
                        //         }
                        //     }
                        // }
                    }
                }
            }
        }
        stage('Images Cleanup') {
            steps{
                script{
                    sh "docker image prune -f"
                }
            }
        }
    }
}
