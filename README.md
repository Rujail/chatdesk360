# chatdesk360

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Project Title & Description

**chatdesk360** is a comprehensive web application designed to streamline customer support and enhance real-time communication. Built using the robust Laravel framework, it provides tools for agents, manages chat interactions, and offers API endpoints for seamless integration with external widgets. This platform aims to facilitate efficient and effective customer engagement through a modern and responsive interface.

## Key Features & Benefits

`chatdesk360` offers a suite of features to empower your customer support operations:

*   **Real-time Chat Functionality:** Enable instant communication between customers and support agents for quick issue resolution.
*   **Agent Management:** Provides administrative tools for managing support agents, their roles, and their interactions.
*   **Secure Customer Authentication:** Implements robust authentication mechanisms for users accessing chat services.
*   **Widget API for Integration:** A dedicated API to easily integrate the chat widget into external websites or applications, enhancing reach and accessibility.
*   **Modern UI with Tailwind CSS:** Utilizes Tailwind CSS for a highly customizable, responsive, and visually appealing user interface.
*   **Laravel Framework:** Leverages the power, security, and conventions of the Laravel framework for a stable and scalable backend.
*   **Efficient Asset Bundling:** Uses Vite for fast and efficient compilation of front-end assets, ensuring optimal performance.

## Prerequisites & Dependencies

Before you begin, ensure you have the following installed on your system:

*   **PHP**: Version 8.1 or higher (recommended for Laravel 10/11)
*   **Composer**: A dependency manager for PHP.
*   **Node.js**: LTS version (e.g., 18.x or 20.x).
*   **npm** or **Yarn**: Package managers for Node.js.
*   **Database**: MySQL, PostgreSQL, SQLite, or another database supported by Laravel.

### Technologies Used:

*   **Languages:**
    *   JavaScript
    *   PHP
*   **Tools & Technologies:**
    *   Laravel Framework
    *   Node.js
    *   Tailwind CSS
    *   Alpine.js
    *   Vite
    *   Axios
    *   Bootstrap Datepicker

## Installation & Setup Instructions

Follow these steps to get `chatdesk360` up and running on your local machine:

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/Rujail/chatdesk360.git
    cd chatdesk360
    ```

2.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```

3.  **Configure Environment Variables:**
    Copy the example environment file and set up your application's configuration.
    ```bash
    cp .env.example .env
    ```
    Open the newly created `.env` file and update the following variables:
    *   `APP_NAME`: Your application's name.
    *   `APP_URL`: The URL where your application will be accessible (e.g., `http://localhost:8000`).
    *   **Database Credentials:**
        ```
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=chatdesk360_db
        DB_USERNAME=your_db_username
        DB_PASSWORD=your_db_password
        ```
    *   Configure other settings like Mail Driver, Cache Driver, etc., as needed.

4.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```

5.  **Run Database Migrations:**
    This will create the necessary tables in your database.
    ```bash
    php artisan migrate
    ```

6.  **Install Node.js Dependencies:**
    ```bash
    npm install # or yarn install
    ```

7.  **Compile Frontend Assets:**
    You can compile assets for development or production:
    ```bash
    npm run dev   # For development (watches for changes)
    # npm run build # For production (optimizes assets)
    ```

8.  **Start the Local Development Server:**
    ```bash
    php artisan serve
    ```
    Your application should now be accessible at `http://127.0.0.1:8000` (or the port specified in your `.env` file).

## Usage Examples & API Documentation

### Web Application Usage

Once installed, navigate to the `APP_URL` in your browser.

*   **Agent Dashboard:** Agents can log in via the `/login` route (or similar, based on `Auth` controllers) to access their dashboard, manage customer conversations, and monitor chat activity.
*   **Customer Interaction:** Customers can initiate chats through the integrated widget on your public-facing website.

### API Usage

The `chatdesk360` repository includes API endpoints for chat authentication and widget interaction.

*   **`app/Http/Controllers/Api/ChatAuthenticationController.php`**: Handles authentication for chat services, likely for verifying users before they can engage in a chat.
*   **`app/Http/Controllers/Api/WidgetApiController.php`**: Provides endpoints for integrating and managing the chat widget on external platforms.

**Example API Endpoint (Conceptual):**

While full API documentation is not provided here, you can infer endpoints from the controller names. For instance:

```http
GET /api/chat/auth/user
POST /api/widget/start-chat
```

Developers intending to integrate the chat widget should consult the code within the `app/Http/Controllers/Api` directory for exact routes, request/response formats, and authentication mechanisms. Consider generating API documentation using tools like OpenAPI/Swagger for better discoverability.

## Configuration Options

`chatdesk360` is highly configurable through various files:

*   **`.env` File:** The primary configuration hub for Laravel applications. Modify `DB_CONNECTION`, `APP_URL`, mail settings, and other environment-specific variables here.
*   **`config/*.php` Files:** Laravel's configuration files (e.g., `config/app.php`, `config/database.php`, etc.) allow fine-grained control over various aspects of the application.
*   **`postcss.config.js`:** Configures PostCSS plugins, specifically Tailwind CSS and Autoprefixer, for processing your CSS.
    ```javascript
    export default {
        plugins: {
            tailwindcss: {},
            autoprefixer: {},
        },
    };
    ```
*   **`tailwind.config.js`:** Customize your Tailwind CSS theme, extend classes, and configure purge paths.
*   **`public/assets/css/chat.css`:** Contains custom CSS variables and styles for the chat interface. You can modify these to match your branding.
    ```css
    :root {
        --primary-color: #2b60d0; /* default primary */
        --popup-bg: #f7f7f7;
        /* ... other custom chat variables */
    }
    ```
*   **`app/Helpers/helpers.php`:** Contains global helper functions that can be used across your application.

## Contributing Guidelines

We welcome contributions to `chatdesk360`! To contribute, please follow these guidelines:

1.  **Fork the Repository:** Start by forking the `chatdesk360` repository to your GitHub account.
2.  **Create a New Branch:**
    ```bash
    git checkout -b feature/your-feature-name # for new features
    # or
    git checkout -b bugfix/issue-description   # for bug fixes
    ```
3.  **Make Your Changes:** Implement your feature or fix, adhering to the existing coding style and best practices (e.g., PSR-12 for PHP).
4.  **Write Clear Commit Messages:** Use descriptive commit messages that explain the purpose of your changes.
    ```bash
    git commit -m "feat: Add new agent dashboard widget"
    ```
5.  **Push to Your Fork:**
    ```bash
    git push origin feature/your-feature-name
    ```
6.  **Submit a Pull Request (PR):** Open a pull request from your forked repository to the `main` branch of `Rujail/chatdesk360`. Provide a detailed description of your changes in the PR.

## License Information

This project currently does not specify a license. It is highly recommended to add a `LICENSE.md` file to the repository to clarify the terms under which this software can be used, modified, and distributed. Without a clear license, the default copyright law applies, limiting others' ability to use and contribute to the project.

## Acknowledgments

We extend our gratitude to the creators and maintainers of the following technologies and libraries that make `chatdesk360` possible:

*   **Laravel Framework**: The elegant PHP framework that serves as the backbone of this application.
*   **Node.js**: The JavaScript runtime for building our frontend assets.
*   **Tailwind CSS**: For providing a utility-first approach to quickly design responsive user interfaces.
*   **Alpine.js**: For adding declarative JavaScript behavior to our HTML with minimal overhead.
*   **Vite**: For blazing-fast frontend development and asset bundling.
*   **Axios**: A promise-based HTTP client for the browser and Node.js.
*   **Bootstrap Datepicker**: For providing a user-friendly date selection component.
