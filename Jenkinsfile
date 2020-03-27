properties([
    parameters([
        string(name: 'BRANCH', defaultValue: 'master', description: "branch"),
        string(name: 'HOSTS', defaultValue: 'xwww70-101', description: "separator: spacja"),
        string(name: 'TARGET_USER', defaultValue: 'stage'),
        booleanParam(name: 'refresh_parameters', defaultValue:  false, description: "odświeża konfiguracje build'a"),
    ]),
    [
        $class: 'BuildDiscarderProperty',
        strategy: [
            $class: 'LogRotator',
            artifactDaysToKeepStr: '',
            artifactNumToKeepStr: '',
            daysToKeepStr: '',
            numToKeepStr: '2'
        ]
    ],
    disableConcurrentBuilds()
])

if (params.refresh_parameters == true) {
    currentBuild.result = 'ABORTED'
    return
}

def mainNode = 'PHP7'
def appName = "tester"
def appBaseDir = "/home/$params.TARGET_USER/$appName"
def appDestinationDir = "$appBaseDir/build-$env.BUILD_NUMBER"

node(mainNode) {

    stage('Clear workspace') {
        deleteDir()
    }

    stage('Destination hosts uptime check') {
        sh "for host in $params.HOSTS; do echo \${host}; ssh $params.TARGET_USER@\${host} \"uptime\"; done"
    }

    stage('Fetch Repository') {
        checkout([
            $class: 'GitSCM',
            branches: [[name: params.BRANCH ]],
            userRemoteConfigs: [[
                url: 'ssh://git@bitbucket.tvn.pl:7999/~kmalek/tester.git'
            ]]
        ])
    }

    stage('Prepare .env file') {
        writeFile file: ".env", text: """
            GMAIL_USER=entertainment.tester@gmail.com
            GMAIL_PASS=zaq1@WSX
            GMAIL_USER_NAME='Entertainment Tester'
        """
    }

    stage('Install Dependencies') {
        sh "composer install --no-dev"
    }

    // stage('Tests') {
    //     sh "./vendor/phpunit/phpunit/phpunit tests/"
    // }

    stage('Create instructions that must to be done after rsync to destination hosts') {
        sh "rm -rf jenkins"
        sh "rm -rf db"
        writeFile file: "jenkins/links.sh", text: """
            mkdir -p $appBaseDir/data/_logs
            mkdir -p $appDestinationDir/.data
            mkdir -p $appBaseDir/data/_db

            ln -fs  $appBaseDir/data/_logs $appDestinationDir/.data/_logs
            ln -fs  $appBaseDir/data/_db $appDestinationDir/db
            ln -fns $appDestinationDir $appBaseDir/build

            chmod -R a+w $appBaseDir/data
        """
        sh "cat jenkins/links.sh"
    }

    stage('Deployment') {
        build job: 'Utils/Utils - RSYNC', parameters: [
            [
                $class: 'LabelParameterValue',
                name: 'node',
                label: mainNode
            ],
            string(name: 'SOURCE_TARGET_USER', value: params.TARGET_USER),
            string(name: 'DESTINATION_HOSTS',  value: params.HOSTS),
            string(name: 'DESTINATION_PATH',   value: appDestinationDir),
            string(name: 'ARTIFACTS_PATH',     value: env.WORKSPACE),
            string(name: 'SOURCE_ENV_NAME',    value: appName)
        ]
    }

}