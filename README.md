# Project Setup Guide

This guide will walk you through the steps to set up and run the project on your system.

## Prerequisites

Before you begin, make sure you have the following software installed on your system:

- Docker
- Composer
- PHP (with Laravel support)
- Postman (for API testing)

## Steps to Set Up the Project

1. **Clone the Project:**
   Clone the project repository into your local system using the following command:

   ```bash
   git clone <project-repo-url>
   ```

2. **Build the Docker Container:**
   Open a terminal in the cloned project directory and run the following command to build the Docker container:

   ```bash
   make build
   ```

3. **Start the Docker Containers:**
   Run the following command to start the Docker containers:

   ```bash
   make up
   ```

4. **Install Composer Dependencies:**
   Once the Docker containers are up and running, open a new terminal window, and navigate to the project directory. Run the following command to install the required Composer dependencies:

   ```bash
   make composer-install
   ```

5. **Seed Data:**
   To populate the database with initial data, run the following command:

   ```bash
   make data
   ```

6. **Import CSV Data:**
   Change to the "webshop" directory within the project, and run the following command to import data from the CSV file into the database:

   ```bash
   cd webshop
   php artisan import_csv_data
   ```

7. **Test the APIs:**
   Use Postman to test the created APIs. Import the provided Postman collection, and start testing the endpoints.

## API Endpoints

- Endpoint 1: [GET] /api/orders - Retrieve all orders.
- Endpoint 2: [GET] /api/orders/{id} - Retrieve a specific order by ID.
- Endpoint 3: [POST] /api/orders - Create a new order.
- Endpoint 4: [PUT] /api/orders/{id} - Update an existing order by ID.
- Endpoint 5: [DELETE] /api/orders/{id} - Delete an order by ID.
- Endpoint 6: [POST] /api/orders/{id}/add - Add new Products to the order.
- Endpoint 7: [POST] /api/orders/{id}/add - Add new Products to the order.

## Additional Notes

- Make sure to have the required CSV file ready before running the data import command.
- Adjust the CSV file path and structure if needed to match your data format.

That's it! Your project should be set up and ready to go. Enjoy developing and testing your API endpoints! If you encounter any issues, refer to the project documentation or reach out to the project maintainers for support. Happy coding!