# Cloud Deployment

## Objective
The purpose of this task is to deploy the Nepal Bulletin Board Online News Portal to a cloud platform so that it can be accessed online for testing, demonstration, and future development.

## Current Progress
Work has started on deployment planning and preparation. The following activities have been identified:
- reviewing cloud platform options
- preparing project files for hosting
- identifying required environment variables
- planning GitHub integration for deployment
- reviewing CI/CD workflow options

## Proposed Platforms
The following cloud platforms are being considered:
- Render
- Heroku
- AWS

Render or Heroku may be used for the initial deployment because they provide easier setup for beginner-level web applications.

## Deployment Requirements
Before deployment, the following items need to be prepared:
- organised frontend and backend structure
- backend server entry point
- package configuration and dependencies
- environment variable setup
- database connection configuration
- production start commands

## Environment Variables
The following variables may be required:
- PORT
- DB_HOST
- DB_USER
- DB_PASSWORD
- DB_NAME
- JWT_SECRET
- NODE_ENV

These values will be stored securely in the cloud hosting platform.

## CI/CD Plan
A basic CI/CD workflow is planned so that updates pushed to GitHub can support automated deployment. This will help reduce manual deployment work and improve development efficiency.

## Next Steps
The next steps are:
- finalise project structure
- prepare backend for deployment
- choose hosting platform
- configure environment variables
- test the application in a live environment
- set up a basic CI/CD workflow

## Status
Deployment work is currently in the planning and setup stage.
