User Api project
========

##Test with curl

###login

    curl -d '{"username":"admin", "password":"1234"}' -H "Content-Type: application/json" -X POST http://www.example.cubex-local.com:6789/login

###list users

    curl -H "token: THE_TOKEN" -X GET http://www.example.cubex-local.com:6789/user

###create user

    curl -d '{"firstName":"TESTUSER", "lastName":"abcdefg", "username":"testusername", "darkMode":false}' -H "Content-Type: application/json" -H "token: THE_TOKEN" -X POST http://www.example.cubex-local.com:6789/user

###get user

    curl -H "token: THE_TOKEN" -X GET http://www.example.cubex-local.com:6789/user/ID

###delete user

    curl -H "token: THE_TOKEN" -X DELETE http://www.example.cubex-local.com:6789/user/ID

###change the name of the user

    curl -d '{"firstName":"TESTUSER", "lastName":"abcdefg"}' -H "Content-Type: application/json" -H "token: THE_TOKEN" -X PATCH http://www.example.cubex-local.com:6789/user/ID/name

###toggle dark mode

    curl -H "token: THE_TOKEN" -X PATCH http://www.example.cubex-local.com:6789/user/ID/dark-mode
