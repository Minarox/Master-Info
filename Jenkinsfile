node {
  stage('SCM') {
    checkout scm
  }
    stage('SonarQube analysis') {
    def scannerHome = tool 'sonarqube-scanner';
    withSonarQubeEnv('SonarQube') { // If you have configured more than one global server connection, you can specify its name
      sh "${scannerHome}/bin/sonar-scanner"
    }
  }
}
