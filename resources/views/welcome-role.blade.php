<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to the System</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-blue-50 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-lg flex w-[900px] overflow-hidden">

        <!-- Left Image -->
        <div class="w-1/2 hidden md:block">
            <img src="{{ asset('images/welcome.jpeg') }}"
                 class="h-full w-full object-cover">
        </div>

        <!-- Right Card -->
        <div class="w-full md:w-1/2 p-10 text-center">

            <img src="{{ asset('images/logo.jpg') }}"
                 class="mx-auto mb-4 h-20 w-20 object-contain">

            <h2 class="text-2xl font-semibold mb-6">
                Welcome to Our System
            </h2>

            <p class="text-gray-500 mt-2 mb-6">
                Please select your role
            </p>

            <form method="GET" action="">
                <select id="role"
                        class="w-full border rounded px-4 py-2 mb-4">
                    <option value="assessor">Assessor</option>
                    <option value="admin">Admin</option>
                    <option value="committee">Committee</option>
                </select>

                <div class="flex justify-center gap-3">
                    <a id="loginBtn"
                    class="bg-blue-600 text-white px-5 py-2 rounded cursor-pointer">
                        Login
                    </a>

                    <a id="registerBtn"
                    class="bg-gray-500 text-white px-5 py-2 rounded cursor-pointer">
                        Register
                    </a>
                </div>

            </form>

        </div>
    </div>

    <script>
        const roleSelect = document.getElementById('role');
        const loginBtn = document.getElementById('loginBtn');
        const registerBtn = document.getElementById('registerBtn');

        function updateLinks() {
            const role = roleSelect.value;

            // Login does NOT depend on role
            loginBtn.href = `/login`;

            // Registration DOES depend on role
            registerBtn.href = `/register?role=${role}`;
        }

        roleSelect.addEventListener('change', updateLinks);
        updateLinks();
    </script>



</body>
</html>
