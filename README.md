## Install and setup the project on local

1) git pull or download this project.
2) on project directory, setup your .env (you can refer to .env.example, but if you do so make to create a database name identical to DB_DATABASE key)
3) run `composer install`
4) run `npm install`
5) run `npm run dev`
6) you are good to go!

## Requirements
- The requirements from the assessment is not detail so it's hard to figure what full architecture will looks but it does mentioned few important requirements. So, I've made research and compile them to fill all the requirements space.
- Following is not my best approach, this just a based on my experiences combine with former colleague's pair programming (and my guess!) and I'm not convinced myself that I know everything. So here goes,

```
UI
- user can download excel file template as seen on assessment context
- it is supported all browser
- it supported responsive screen
- upload file accept csv format only
- uploading process will use ajax instead of form submit
- cancel button basically clear the uploading process
- the record listing will re-updated real time everytime when there's new file uploaded
- the record listing will displayed in pagination and have 10 item displayed per page
- there's success/error message bar appeared after uploaded

Functionality
- no auth, middleware, policy, gates setup
- no modular design for blade template
- use local DB, and consist of one table only for student records
- will have frontend file validation
- will have frontend file content validation
- data duplication wont store in the db
- data duplication will be based on all fields name, if one of them not is not matched, the data will consider non-duplicate data
- temporary stored files in system directory will be cleared whenever the data extraction from excel file is done
```

All things above, have included in the codebase. 

## Live hosting
- I've hosted this project to Heroku with circleCI manager. Here is the [link](http://studentqr-assessment-fitri.herokuapp.com/students/dashboard) .

Cheers!




