# Scrum

This app was built to assist Scrum teams in their various activities related to running the team.

## Build Steps
### Backend
1. Clone the repo to your preferred location on your local computer
2. cd into the project and type `composer install`
3. Next you will need to create your database and an account on [Pusher](https://www.pusher.com)
4. Next create your .env file by typing `cp .env.example .env`
5. You will want to update your `.env` with the values for your database connection and Pusher
6. You will also want to update your mailer settings so the system can send out invitations to team members
7. Now, you will need to generate a new app key. This can be done in the command line by running `php artisan key:generate`
8. Finally, we need to build the database which is done by running `php artisan migrate`

### Frontend
The frontend of the project uses Vuejs so we will need to build the app. This can be done by running:
```
npm install
npm run dev
```
You can also run this if you want it to automatically rebuild on save: 
```
npm run watch
```
And, of course, you will want to run this in production:
```
npm run prod
```
Once you have done this, you should be able to bring up your app at localhost or whatever is pointing at your server
and it should bring up the login screen for the app. The app does not expose a link to register a user because we want
new users to be invited. The registration page still works, however, and should be used to create your first user. This
page is found at `{base_url}/register`.
