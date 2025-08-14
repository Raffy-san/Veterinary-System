<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Medical Record System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<body class="bg-green-100 flex items-center justify-center min-h-screen">
    <section class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <div class="w-full max-w-md flex justify-center mb-2">
            <i class="fa-solid fa-paw text-6xl text-green-500 rotate-45"></i>
        </div>
        <h2 class="text-2xl font-normal text-center text-gray-800">Veterinary System</h2>
        <h3 class="text-xl font-light mb-4 text-center text-gray-700">Sign in to access your dashboard</h3>
        <form>
            <div class="mb-4">
                <label class="font-bold block text-gray-700 mb-2" for="custom-select">Access Type</label>
                <div class="relative inline-block w-full">
                    <div class="bg-white border border-gray-300 rounded-md p-2 cursor-pointer"
                        id="custom-select-trigger">
                        Login As
                    </div>
                    <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden"
                        id="custom-select-options">
                        <li class="group p-2 hover:bg-green-500 hover:text-white cursor-pointer flex items-center"
                            data-value="option1">
                            <i
                                class="fa-solid fa-paw mr-2 text-green-500 rotate-45 group-hover:text-white transition-colors"></i>
                            Veterinarian
                        </li>
                        <li class="group p-2 hover:bg-green-500 hover:text-white cursor-pointer flex items-center"
                            data-value="option2">
                            <i
                                class="fa-solid fa-paw mr-2 text-green-500 rotate-45 group-hover:text-white transition-colors"></i>
                            Pet Owner
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mb-6">
                <label class="font-bold block text-gray-700 mb-2" for="username">Username</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500"
                    type="text" id="username" name="username" placeholder="Enter Your Username" required>
            </div>
            <div class="mb-6">
                <label class="font-bold block text-gray-700 mb-2" for="password">Password</label>
                <input class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500"
                    type="password" id="password" name="password" placeholder="Enter Your Password" required>
            </div>
            <button
                class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-700 transition-colors font-semibold"
                type="submit">
                Sign in
            </button>
        </form>
    </section>
    <script>
        document.getElementById('custom-select-trigger').addEventListener('click', function () {
            document.getElementById('custom-select-options').classList.toggle('hidden');
        });

        document.querySelectorAll('#custom-select-options li').forEach(item => {
            item.addEventListener('click', function () {
                // Get the icon HTML and the text
                const iconHTML = this.querySelector('i').outerHTML;
                const text = this.textContent.trim();

                // Set the trigger's HTML to icon + text
                document.getElementById('custom-select-trigger').innerHTML = iconHTML + '<span>' + text + '</span>';
                document.getElementById('custom-select-options').classList.add('hidden');
                // You can also capture the data-value here for form submission
                console.log('Selected:', this.dataset.value);
            });
        });
    </script>
</body>

</html>