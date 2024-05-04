# Task Management API

This API allows you to manage tasks within a project management application. It provides endpoints for creating, updating, deleting tasks, setting task assignments, and retrieving task information.

## Authentication

Authentication for this API is handled using Sanctum. You need to obtain a token by registering, logging in, or resetting your password using the provided endpoints. Once you have a token, include it in the Authorization header with the Bearer scheme for authenticated requests.

### Register
- **POST** `localhost:8000/api/sanctum/register`
    - **Body**:
      ```json
      {
          "name":"merey",
          "email":"test@test.com",
          "password":"12345678"
      }
      ```
    - **Response**:
      ```json
      {
          "token": "6|VnNMwTiucDoTb7Fr3eGmjWw3QPufrGdRXpk1nIoY4dbf515a"
      }
      ```

### Login
- **POST** `localhost:8000/api/sanctum/login`
    - **Body**:
      ```json
      {
          "email":"test@test.com",
          "password":"12345678"
      }
      ```
    - **Response**:
      ```json
      {
          "token": "7|WkFOno1S5xXwa3eBAkuKmgMbrAX08EDA1spu6lba585df327"
      }
      ```

### Forgot Password
- **POST** `localhost:8000/api/sanctum/forgot`
    - **Body**:
      ```json
      {
          "email":"test@test.com"
      }
      ```
    - **Response**:
      ```json
      {
          "message": "Password reset email sent successfully"
      }
      ```

### Reset Password
- **POST** `localhost:8000/api/sanctum/password/reset`
    - **Body**:
      ```json
      {
          "email":"test@test.com",
          "password":"NEW12345678",
          "reset_code":"jcfLH5"
      }
      ```

## Task Routes

### Create Task
- **POST** `localhost:8000/api/tasks/`
    - **Body**:
      ```json
      {
          "title": "Complete API Development",
          "description": "Develop the API endpoints for the new project management application.",
          "status": "new",
          "priority": 2,
          "start_date": "",
          "due_duration": "1 days 5 hours 0 minutes",
          "user_id":null
      }
      ```
    - **Response**:
      ```json
      {
          "message": "Task created successfully!",
          "task": {
              "title": "Complete API Development",
              "description": "Develop the API endpoints for the new project management application.",
              "status": "new",
              "priority": 2,
              "start_date": "2024-05-04 15:50:40",
              "due_date": "2024-05-05 20:50:40",
              "user_id": null,
              "updated_at": "2024-05-04T10:50:40.000000Z",
              "created_at": "2024-05-04T10:50:40.000000Z",
              "id": 12
          }
      }
      ```

### Update Task
- **PUT** `localhost:8000/api/tasks/`
    - **Body**:
      ```json
      {
          "title": "Complete API Development",
          "description": "Develop the API endpoints for the new project management application.",
          "status": "new",
          "priority": 2,
          "start_date": "",
          "due_duration": "1 days 5 hours 0 minutes",
          "user_id":null
      }
      ```
    - **Response**:
      ```json
      {
          "message": "Task updated successfully!",
          "task": {
              "title": "Complete API Development",
              "description": "Develop the API endpoints for the new project management application.",
              "status": "new",
              "priority": 2,
              "start_date": "2024-05-04 15:50:40",
              "due_date": "2024-05-05 20:50:40",
              "user_id": null,
              "updated_at": "2024-05-04T10:50:40.000000Z",
              "created_at": "2024-05-04T10:50:40.000000Z",
              "id": 12
          }
      }
      ```

### Delete Task
- **DELETE** `localhost:8000/api/tasks/`
    - **Query Params**: `id=1`
    - **Response**:
      ```json
      {
          "message": "Task deleted successfully!"
      }
      ```

### Set Task
- **PATCH** `localhost:8000/api/tasks/set`
    - **Body**:
      ```json
      {
          "task_id":1,
          "user_id":1
      }
      ```
    - **Response**:
      ```json
      {
          "message": "Task successfully set"
      }
      ```

### Get All Tasks
- **GET** `localhost:8000/api/tasks/get?page=1`
    - **Response**: Tasks with pagination

### Get Task by ID
- **GET** `localhost:8000/api/tasks/get/12`
    - **Response**:
      ```json
      {
          "id": 12,
          "title": "Complete API Development",
          "description": "Develop the API endpoints for the new project management application.",
          "status": "new",
          "priority": 2,
          "start_date": "2024-05-04 15:50:40",
          "due_date": "2024-05-05 20:50:40",
          "user_id": null,
          "created_at": "2024-05-04T10:50:40.000000Z",
          "updated_at": "2024-05-04T10:50:40.000000Z",
          "deleted_at": null
      }
      ```

## Note
- Ensure to replace placeholders like `host_name`, `database_name`, `username`, `password`, etc., with your actual credentials in the `.env` file.
- All routes under `/api/tasks` require authentication using Sanctum. Include the token in the Authorization header for these requests.
