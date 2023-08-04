# NOTES API

### MYSQL + SYMFONY

### INSTALLATION

Installation of dependencies
>composer install


Database is deployed, use the connection string provided in your .env file.

Run the server
>symfony server:start


## ENDPOINTS

All endpoints are available in this Postman collection

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/24034971-81770311-10e8-4c7b-98d7-90d63077c46e?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D24034971-81770311-10e8-4c7b-98d7-90d63077c46e%26entityType%3Dcollection%26workspaceId%3D575fae1e-0ea4-48f2-9118-b9c123e9f1bc)


### NOTES

Complete CRUD + extras

* Create note - provide title, description, user ID and array of categories, date is automatically added
* Update note - using the id, provide title, description or both
* Delete note - using its id
* Add category to note
* Delete category from note
* Get all notes
* Get note by ID
* Get past notes - notes from seven days before the petition
* Get notes by category
* Get notes by user

### USER

Complete CRUD

* Create user - provide name, email and age
* Update user - provide name, age or both + user's id
* Delete user - using the user's id
* Get all users
* Get user by ID

### CATEGORIES

Complete CRUD

* Create category - provide a name
* Update category - change the name
* Delete category - using the id
* Get all categories


