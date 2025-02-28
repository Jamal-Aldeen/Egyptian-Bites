# Egyptian-Bites Documentation 📚  

Welcome to the **further extended documentation** for **Egyptian-Bites**, a comprehensive web-based restaurant management system. This documentation provides an even deeper dive into the project structure, technologies used, features, examples, advanced use cases, and additional resources to help you fully understand and utilize the system.

---

## 1. Project Structure 🗂️  

The **Egyptian-Bites** project is organized into a modular structure to ensure scalability, maintainability, and ease of development. Below is the **further extended breakdown** of the project structure:

### Root Directory
```
Egyptian-Bites/
├── .git/                     # Git version control files
├── config/                   # Configuration files
│   ├── db.php                # Database connection settings
│   ├── restaurant_db.sql     # SQL file for database schema
│   ├── stripe.php            # Stripe payment gateway configuration
│   └── email.php             # Email configuration for notifications
├── controllers/              # Application controllers
│   ├── AuthController.php    # Handles authentication logic
│   ├── InventoryController.php # Manages inventory operations
│   ├── MenuController.php    # Handles menu-related operations
│   ├── OrderController.php   # Manages order processing
│   ├── PaymentController.php # Handles payment processing
│   ├── ReservationController.php # Manages reservations
│   ├── SalesController.php   # Handles sales and reporting
│   └── UserController.php    # Handles user management
├── handlers/                 # Handlers for specific tasks
│   ├── inventory-handler.php # Handles inventory-related requests
│   ├── menu-handler.php      # Handles menu-related requests
│   ├── reservation-handler.php # Handles reservation-related requests
│   ├── sales-handler.php     # Handles sales-related requests
│   ├── user-handler.php      # Handles user-related requests
│   ├── logout.php            # Handles user logout
│   ├── password-reset-code.php # Handles password reset logic
│   └── notification-handler.php # Handles notification logic
├── models/                   # Database models
│   ├── DataBaseAct.php       # Database actions and queries
│   ├── Inventory.php         # Inventory management model
│   ├── Menu.php              # Menu management model
│   ├── Order.php             # Order management model
│   ├── Payment.php           # Payment management model
│   ├── Reservation.php       # Reservation management model
│   ├── User.php              # User management model
│   ├── Validation.php        # Validation logic for forms and inputs
│   └── Notification.php      # Notification management model
├── public/                   # Publicly accessible files
│   ├── assets/               # Static assets (images, CSS, JS)
│   ├── css/                  # Custom CSS files
│   ├── js/                   # Custom JavaScript files
│   └── uploads/              # Uploaded files (e.g., menu images, profile pictures)
├── views/                    # Frontend views
│   ├── customer/             # Views for customers
│   │   ├── cart.php          # Shopping cart view
│   │   ├── menu.php          # Menu view
│   │   ├── order-tracking.php # Order tracking view
│   │   ├── profile.php       # Customer profile view
│   │   ├── reservation.php   # Reservation booking view
│   │   └── notifications.php # Notifications view
│   ├── layouts/              # Layout templates
│   │   ├── header.php        # Header template
│   │   ├── footer.php        # Footer template
│   │   └── sidebar.php       # Sidebar template for staff dashboard
│   ├── shared/               # Shared views
│   │   ├── login.php         # Login page
│   │   ├── register.php      # Registration page
│   │   ├── password-reset.php # Password reset page
│   │   └── forgot-password.php # Forgot password page
│   └── staff/                # Views for staff/admin
│       ├── dashboard.php     # Staff dashboard
│       ├── inventory-management.php # Inventory management view
│       ├── menu-management.php # Menu management view
│       ├── reservations.php  # Reservation management view
│       ├── reports.php       # Sales reports view
│       ├── user-management.php # User management view
│       └── notifications.php # Staff notifications view
├── vendor/                   # Composer dependencies
├── .env.example              # Example environment file
├── composer.json             # Composer configuration
├── composer.lock             # Composer lock file
├── README.md                 # Project overview and instructions
└── index.php                 # Entry point for the application
```

---

## 2. Technologies Used 🛠️  

The **Egyptian-Bites** project leverages a variety of technologies to ensure a robust, scalable, and user-friendly application. Below is a **further extended list** of the technologies used:

### Backend
- **PHP**: The core server-side scripting language used for backend logic.
- **MySQL**: The relational database management system for storing application data.
- **PDO (PHP Data Objects)**: A secure and consistent interface for database access.
- **Composer**: A dependency manager for PHP, used to manage libraries and packages.
- **PHPMailer**: A library for sending email notifications (e.g., password reset, order confirmation).
- **Stripe API**: Integrated for secure online payment processing.
- **JWT (JSON Web Tokens)**: Used for secure user authentication and session management.

### Frontend
- **HTML5**: The standard markup language for creating web pages.
- **CSS3**: Used for styling the application and ensuring a responsive design.
- **JavaScript**: Adds interactivity and dynamic behavior to the application.
- **Bootstrap 5**: A frontend framework for building responsive and mobile-first websites.
- **FontAwesome**: A library of icons used throughout the application.
- **SweetAlert2**: A JavaScript library for beautiful and customizable alerts.
- **Chart.js**: Used for generating sales reports and visualizations.
- **AJAX**: Used for asynchronous data fetching and updating the UI without reloading the page.

### Additional Tools
- **Git**: Version control system for tracking changes and collaborating on the project.
- **Apache/Nginx**: Web servers used for hosting the application.
- **Mailtrap**: Used for testing email functionality during development.
- **XAMPP/WAMP**: Local development environments for testing the application.
- **Postman**: Used for testing API endpoints during development.
- **Docker**: Used for containerizing the application for easy deployment.

---

## 3. Features 🌟  

The **Egyptian-Bites** system is packed with features to help restaurant owners and staff manage their operations efficiently. Below is a **further extended breakdown** of the features:

### User Management
- **Role-Based Access Control**: 
  - Staff and administrators have different levels of access.
  - Admins can manage users, update roles, and delete accounts.
- **User Profiles**:
  - Users can update their profile information, including name, email, and profile picture.
  - Password management with secure hashing and validation.
- **Email Verification**:
  - Users receive a verification email upon registration.
  - Email verification is required to activate the account.
- **Password Recovery**:
  - Users can reset their password via email if they forget it.

### Menu Management
- **Menu Categories**:
  - Add, edit, and delete menu categories (e.g., Appetizers, Main Course, Desserts).
- **Menu Items**:
  - Add, edit, and delete menu items with details like name, description, price, and image.
  - Set availability status for each item.
- **Special Offers**:
  - Create and manage special offers with discount types (percentage or fixed) and validity dates.
  - Automatically apply discounts to menu items during checkout.

### Inventory Management
- **Stock Tracking**:
  - Track inventory levels for each item.
  - Set reorder thresholds to receive alerts when stock is low.
- **Add/Delete Items**:
  - Add new items to the inventory or delete outdated items.
- **Low Stock Notifications**:
  - Staff receive notifications when inventory items fall below the reorder threshold.

### Order Management
- **Order Placement**:
  - Customers can place orders through the menu interface.
  - Orders are stored in the database with details like total price, status, and payment method.
- **Order Tracking**:
  - Customers can track the status of their orders (e.g., Pending, Preparing, Delivered).
- **Payment Processing**:
  - Integrated with Stripe for secure online payments.
  - Support for cash on delivery.
- **Order History**:
  - Customers can view their past orders and reorder items.

### Reservation Management
- **Reservation Booking**:
  - Customers can book tables by providing details like date, time, and number of guests.
- **Reservation Status**:
  - Staff can confirm or cancel reservations through the dashboard.
  - Real-time updates on reservation status.
- **Reservation History**:
  - Customers can view their past reservations.

### Dashboard & Reports
- **Key Metrics**:
  - View total sales, active orders, and upcoming reservations on the dashboard.
- **Sales Reports**:
  - Generate detailed sales reports filtered by date range.
  - Export reports in CSV or PDF format.
- **Inventory Reports**:
  - Generate reports on inventory levels and low-stock items.

### Notifications
- **Low Stock Alerts**:
  - Receive notifications when inventory items fall below the reorder threshold.
- **Order Updates**:
  - Customers receive notifications when their order status changes.
- **Reservation Updates**:
  - Customers receive notifications when their reservation status changes.

---

## 4. Examples and Use Cases 🧩  

Below are **further extended examples and use cases** to help you understand how to use the **Egyptian-Bites** system effectively:

### Example 1: Adding a New Menu Item
1. **Navigate to Menu Management**:
   - Go to the "Menu Management" section in the staff dashboard.
2. **Add a New Item**:
   - Fill in the form with details like name, description, price, and category.
   - Upload an image for the menu item.
3. **Save the Item**:
   - Click "Add Item" to save the new menu item to the database.
4. **Set Availability**:
   - Toggle the availability status to make the item visible to customers.

### Example 2: Placing an Order
1. **Browse the Menu**:
   - Customers can browse the menu and add items to their cart.
2. **Checkout**:
   - Proceed to checkout and provide payment details (credit card or cash on delivery).
3. **Order Confirmation**:
   - After successful payment, the order is confirmed, and the customer receives a notification.
4. **Track Order**:
   - Customers can track the status of their order in real-time.

### Example 3: Managing Reservations
1. **Book a Table**:
   - Customers can book a table by selecting a date, time, and number of guests.
2. **Confirm Reservation**:
   - Staff can confirm the reservation from the dashboard.
3. **Cancel Reservation**:
   - If needed, staff can cancel the reservation, and the customer will be notified.
4. **View Reservation History**:
   - Customers can view their past reservations and rebook tables.

### Example 4: Generating Sales Reports
1. **Filter by Date Range**:
   - Select a start and end date to filter sales data.
2. **Generate Report**:
   - Click "Generate Report" to view the sales data.
3. **Export Report**:
   - Export the report in CSV or PDF format for further analysis.
4. **View Key Metrics**:
   - View total sales, active orders, and upcoming reservations on the dashboard.

### Example 5: Managing Inventory
1. **Add New Item**:
   - Go to the "Inventory Management" section and add a new item with details like name, quantity, and reorder threshold.
2. **Track Stock Levels**:
   - Monitor stock levels and receive notifications when items fall below the reorder threshold.
3. **Delete Outdated Items**:
   - Remove outdated items from the inventory to keep it up-to-date.

---

## 5. Advanced Use Cases 🚀  

### Use Case 1: Integrating with Third-Party APIs
- **Stripe Integration**:
  - The system integrates with Stripe for secure online payments.
  - Customers can pay using credit/debit cards, and payment details are securely processed.
- **Email Notifications**:
  - The system uses PHPMailer to send email notifications for order confirmations, password resets, and reservation updates.

### Use Case 2: Customizing the Dashboard
- **Custom Metrics**:
  - Admins can customize the dashboard to display key metrics like total sales, active orders, and low-stock items.
- **Custom Reports**:
  - Generate custom reports based on specific criteria like date range, menu category, or payment method.

### Use Case 3: Scaling the System
- **Database Optimization**:
  - The system uses MySQL with PDO for efficient database operations.
  - Indexes and optimized queries ensure fast performance even with large datasets.
- **Modular Architecture**:
  - The modular structure allows for easy scaling and addition of new features.

---

## 6. Troubleshooting and Support 🛠️  

### Common Issues
- **Database Connection Errors**:
  - Ensure the database credentials in `config/db.php` are correct.
  - Check if the MySQL server is running.
- **Email Notifications Not Working**:
  - Verify the SMTP settings in `config/email.php`.
  - Ensure the email server is properly configured.

---

### 7. Detailed Explanation of All Concepts and Terms 📖

In this section, we will dive deeper into the **PHP**, **MySQL**, and **library-related concepts** used in the Egyptian-Bites project. Understanding these concepts is crucial for working with the backend logic, database interactions, and third-party integrations.

---

### **A. PHP Concepts**

#### 1. **PHP (Hypertext Preprocessor)**
PHP is a server-side scripting language used for web development. It is embedded within HTML and executed on the server, generating dynamic content for the client.

- **Why PHP?**
  - **Ease of Use**: PHP is beginner-friendly and has a simple syntax.
  - **Database Integration**: PHP works seamlessly with MySQL, making it ideal for database-driven applications like Egyptian-Bites.
  - **Wide Adoption**: PHP powers a large portion of the web, including platforms like WordPress and Facebook.

- **Example in Egyptian-Bites**:
  ```php
  <?php
  echo "Welcome to Egyptian-Bites!";
  ?>
  ```

---

#### 2. **PDO (PHP Data Objects)**
PDO is a database access layer in PHP that provides a consistent interface for accessing databases. It supports multiple database systems (e.g., MySQL, PostgreSQL, SQLite).

- **Why PDO?**
  - **Security**: PDO uses prepared statements, which prevent SQL injection attacks.
  - **Flexibility**: You can switch databases without changing much code.
  - **Error Handling**: PDO provides robust error handling mechanisms.

- **Example in Egyptian-Bites**:
  ```php
  $pdo = new PDO("mysql:host=localhost;dbname=restaurant_db", "root", "");
  $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = :email");
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  ```

---

#### 3. **Sessions**
Sessions are used to store user-specific data across multiple pages. In Egyptian-Bites, sessions are used to manage user authentication and store temporary data like cart items.

- **Why Sessions?**
  - **State Management**: Sessions allow the server to remember user data (e.g., login status, cart items) across requests.
  - **Security**: Session data is stored on the server, making it more secure than cookies.

- **Example in Egyptian-Bites**:
  ```php
  session_start();
  $_SESSION['user_id'] = $user['id']; // Store user ID in session
  ```

---

#### 4. **Form Handling**
PHP is used to process form data submitted by users. This includes validating inputs, sanitizing data, and interacting with the database.

- **Why Form Handling?**
  - **Data Collection**: Forms are the primary way users interact with the application (e.g., login, registration, order placement).
  - **Validation**: Ensures that the data entered by users is correct and secure.

- **Example in Egyptian-Bites**:
  ```php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = htmlspecialchars($_POST['email']);
      $password = $_POST['password'];
      // Validate and process the form data
  }
  ```

---

#### 5. **Error Handling**
PHP provides mechanisms to handle errors and exceptions gracefully. This ensures that the application doesn’t crash when something goes wrong.

- **Why Error Handling?**
  - **User Experience**: Prevents users from seeing raw error messages.
  - **Debugging**: Helps developers identify and fix issues.

- **Example in Egyptian-Bites**:
  ```php
  try {
      $stmt = $pdo->prepare("INSERT INTO Users (email, password) VALUES (?, ?)");
      $stmt->execute([$email, $hashedPassword]);
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
  }
  ```

---

### **B. MySQL Concepts**

#### 1. **Relational Database**
MySQL is a relational database management system (RDBMS) that stores data in tables with rows and columns. Relationships between tables are established using foreign keys.

- **Why MySQL?**
  - **Scalability**: MySQL can handle large datasets efficiently.
  - **ACID Compliance**: Ensures data integrity with transactions.
  - **Wide Adoption**: MySQL is one of the most popular databases for web applications.

- **Example in Egyptian-Bites**:
  ```sql
  CREATE TABLE Users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(100) NOT NULL,
      password VARCHAR(255) NOT NULL
  );
  ```

---

#### 2. **SQL Queries**
SQL (Structured Query Language) is used to interact with the database. Common operations include **SELECT**, **INSERT**, **UPDATE**, and **DELETE**.

- **Why SQL?**
  - **Data Retrieval**: Fetch data from the database based on specific criteria.
  - **Data Manipulation**: Add, update, or delete records in the database.

- **Example in Egyptian-Bites**:
  ```sql
  SELECT * FROM Orders WHERE user_id = 1;
  INSERT INTO Orders (user_id, total_price) VALUES (1, 25.50);
  UPDATE Users SET email = 'new@example.com' WHERE id = 1;
  DELETE FROM Reservations WHERE id = 5;
  ```

---

#### 3. **Indexes**
Indexes are used to speed up data retrieval by creating a quick lookup mechanism for specific columns.

- **Why Indexes?**
  - **Performance**: Improves query performance, especially for large datasets.
  - **Efficiency**: Reduces the time needed to search for records.

- **Example in Egyptian-Bites**:
  ```sql
  CREATE INDEX idx_email ON Users (email);
  ```

---

#### 4. **Foreign Keys**
Foreign keys are used to establish relationships between tables. They ensure referential integrity by linking a column in one table to a primary key in another table.

- **Why Foreign Keys?**
  - **Data Integrity**: Ensures that relationships between tables are maintained.
  - **Cascading Actions**: Automatically update or delete related records.

- **Example in Egyptian-Bites**:
  ```sql
  CREATE TABLE Orders (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT,
      FOREIGN KEY (user_id) REFERENCES Users(id)
  );
  ```

---

#### 5. **Transactions**
Transactions are used to group multiple SQL operations into a single unit of work. If any operation fails, the entire transaction is rolled back.

- **Why Transactions?**
  - **Data Consistency**: Ensures that all operations are completed successfully or none at all.
  - **Error Recovery**: Prevents partial updates in case of errors.

- **Example in Egyptian-Bites**:
  ```php
  $pdo->beginTransaction();
  try {
      $stmt1 = $pdo->prepare("INSERT INTO Orders (user_id, total_price) VALUES (?, ?)");
      $stmt1->execute([1, 25.50]);
      
      $stmt2 = $pdo->prepare("UPDATE Inventory SET quantity = quantity - 1 WHERE id = ?");
      $stmt2->execute([5]);
      
      $pdo->commit();
  } catch (Exception $e) {
      $pdo->rollBack();
      echo "Transaction failed: " . $e->getMessage();
  }
  ```

---

### **C. Library-Related Concepts**

#### 1. **Composer**
Composer is a dependency manager for PHP. It allows you to manage third-party libraries and packages used in your project.

- **Why Composer?**
  - **Dependency Management**: Automatically installs and updates libraries.
  - **Autoloading**: Automatically loads classes and files, reducing manual includes.

- **Example in Egyptian-Bites**:
  ```json
  {
      "require": {
          "phpmailer/phpmailer": "^6.5",
          "stripe/stripe-php": "^7.0"
      }
  }
  ```

---

#### 2. **PHPMailer**
PHPMailer is a library for sending emails in PHP. It supports SMTP, HTML emails, and attachments.

- **Why PHPMailer?**
  - **Ease of Use**: Simplifies sending emails compared to PHP's native `mail()` function.
  - **Security**: Supports secure email sending via SMTP.

- **Example in Egyptian-Bites**:
  ```php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host = 'smtp.example.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'user@example.com';
  $mail->Password = 'password';
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;

  $mail->setFrom('no-reply@egyptianbites.com', 'Egyptian Bites');
  $mail->addAddress($userEmail);
  $mail->Subject = 'Order Confirmation';
  $mail->Body = 'Thank you for your order!';

  $mail->send();
  ```

---

#### 3. **Stripe API**
Stripe is a payment processing platform that allows businesses to accept online payments. The Stripe PHP library is used to integrate Stripe into Egyptian-Bites.

- **Why Stripe?**
  - **Security**: Stripe handles sensitive payment information securely.
  - **Ease of Integration**: The Stripe PHP library makes it easy to process payments.

- **Example in Egyptian-Bites**:
  ```php
  \Stripe\Stripe::setApiKey('sk_test_1234567890');
  $charge = \Stripe\Charge::create([
      'amount' => 1000, // $10.00
      'currency' => 'usd',
      'source' => $token,
  ]);
  ```

---

#### 4. **SweetAlert2**
SweetAlert2 is a JavaScript library for creating beautiful and customizable alerts. It is used in Egyptian-Bites to provide feedback to users (e.g., success messages, error alerts).

- **Why SweetAlert2?**
  - **User Experience**: Provides a better user experience compared to native JavaScript alerts.
  - **Customization**: Allows for custom icons, buttons, and animations.

- **Example in Egyptian-Bites**:
  ```javascript
  Swal.fire({
      icon: 'success',
      title: 'Order Placed!',
      text: 'Your order has been successfully placed.',
  });
  ```

---

### **D. Why These Concepts Are Used 🧠**

1. **PHP**: Provides the backend logic for handling user requests, processing data, and interacting with the database.
2. **MySQL**: Stores all the application data, including user information, orders, reservations, and inventory.
3. **PDO**: Ensures secure and efficient database interactions.
4. **Composer**: Manages third-party libraries like PHPMailer and Stripe, making the application more powerful and secure.
5. **PHPMailer**: Simplifies sending emails for notifications, password resets, and order confirmations.
6. **Stripe**: Enables secure online payments, a critical feature for any e-commerce or restaurant management system.
7. **SweetAlert2**: Enhances the user experience by providing visually appealing feedback.

---

### **E. Summary of Key Concepts 📝**

| **Concept**         | **Purpose**                                                                 |
|----------------------|-----------------------------------------------------------------------------|
| PHP                 | Server-side scripting for backend logic.                                   |
| MySQL               | Relational database for storing application data.                          |
| PDO                 | Secure and consistent database access.                                     |
| Sessions            | Manage user-specific data across multiple pages.                           |
| Form Handling       | Process and validate user input.                                           |
| Error Handling      | Prevent crashes and handle issues gracefully.                              |
| SQL Queries         | Interact with the database (SELECT, INSERT, UPDATE, DELETE).               |
| Foreign Keys        | Establish relationships between tables.                                    |
| Transactions        | Ensure data consistency by grouping multiple SQL operations.               |
| Composer            | Manage third-party libraries and dependencies.                             |
| PHPMailer           | Send emails for notifications and confirmations.                           |
| Stripe API          | Process online payments securely.                                          |
| SweetAlert2         | Provide visually appealing feedback to users.                              |

---

### **F. Additional Resources 📚**

- **PHP Documentation**: https://www.php.net/docs.php
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **PDO Tutorial**: https://www.php.net/manual/en/book.pdo.php
- **Composer Documentation**: https://getcomposer.org/doc/
- **PHPMailer Documentation**: https://github.com/PHPMailer/PHPMailer
- **Stripe API Documentation**: https://stripe.com/docs/api
- **SweetAlert2 Documentation**: https://sweetalert2.github.io/

---

By understanding these concepts, you’ll be well-equipped to work with the Egyptian-Bites project and extend its functionality as needed. Happy coding! 🚀

## 8. Additional Resources 📚

### Documentation
- **PHP Documentation**: [https://www.php.net/docs.php](https://www.php.net/docs.php)
- **MySQL Documentation**: [https://dev.mysql.com/doc/](https://dev.mysql.com/doc/)
- **Bootstrap Documentation**: [https://getbootstrap.com/docs/](https://getbootstrap.com/docs/)
- **Stripe API Documentation**: [https://stripe.com/docs/api](https://stripe.com/docs/api)

### Tutorials
- **PHP Tutorials**: [https://www.w3schools.com/php/](https://www.w3schools.com/php/)
- **MySQL Tutorials**: [https://www.w3schools.com/sql/](https://www.w3schools.com/sql/)
- **Bootstrap Tutorials**: [https://www.w3schools.com/bootstrap/](https://www.w3schools.com/bootstrap/)

---

## Conclusion 🎉

The **Egyptian-Bites** system is a powerful tool for managing restaurant operations efficiently. With its modular structure, robust features, and user-friendly interface, it is designed to meet the needs of modern dining establishments. Whether you're managing users, updating the menu, tracking inventory, or generating reports, **Egyptian-Bites** has you covered.

For any questions or feedback, feel free to reach out to the development team. Happy managing! 🚀