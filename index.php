<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My PHP Project</title>
     <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-gray-900">Users</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all the users in your account including their name, title, email and role.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <button type="button" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add user</button>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Title</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            // Define an array of users
                            $users = [
                                ['name' => 'Lindsay Walton', 'title' => 'Front-end Developer', 'email' => 'lindsay.walton@example.com', 'role' => 'Member'],
                                ['name' => 'Rom Walton', 'title' => 'Front-end Developer', 'email' => 'lindsay.walton@example.com', 'role' => 'Member'],
                                ['name' => 'Lindsay Walton', 'title' => 'Front-end Developer', 'email' => 'lindsay.walton@example.com', 'role' => 'Member'],
                                ['name' => 'Lindsay Walton', 'title' => 'Front-end Developer', 'email' => 'lindsay.walton@example.com', 'role' => 'Member'],
                                // Add more users here
                                // ['name' => 'User Name', 'title' => 'User Title', 'email' => 'User Email', 'role' => 'User Role'],
                            ];

                            // Iterate over the users array to display each user
                            foreach ($users as $user) {
                                echo "<tr>";
                                echo "<td class='whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0'>{$user['name']}</td>";
                                echo "<td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>{$user['title']}</td>";
                                echo "<td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>{$user['email']}</td>";
                                echo "<td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>{$user['role']}</td>";
                                echo "<td class='relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0'>";
                                echo "<a href='#' class='text-indigo-600 hover:text-indigo-900'>Edit<span class='sr-only'>, {$user['name']}</span></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
