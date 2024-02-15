<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navbar dengan Dropdown - Contoh</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
  <nav class="bg-blue-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <a href="#" class="text-white text-xl font-semibold">Logo</a>
        </div>

        <!-- Menu Utama -->
        <div class="hidden sm:block">
          <ul class="ml-6 flex space-x-4">
            <li>
              <a href="#" class="text-white hover:text-gray-200">Beranda</a>
            </li>
            <li>
              <a href="#" class="text-white hover:text-gray-200">Tentang</a>
            </li>
            <li class="relative">
              <button type="button" class="text-white hover:text-gray-200 focus:outline-none">
                Layanan
                <svg class="ml-1 h-5 w-5 text-gray-300 inline-flex" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 19l-7-7h14l-7 7zm0-9l-7-7h14l-7 7z" clip-rule="evenodd" />
                </svg>
              </button>
              <div class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none hidden">
                <div class="py-1">
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Layanan 1</a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Layanan 2</a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Layanan 3</a>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Mobile Menu (Hamburger Menu) -->
    <div class="sm:hidden">
      <div class="px-2 pt-2 pb-3 space-y-1">
        <a href="#" class="text-white block px-3 py-2 rounded-md text-base font-medium">Beranda</a>
        <a href="#" class="text-white block px-3 py-2 rounded-md text-base font-medium">Tentang</a>
        <div class="relative">
          <button type="button" class="text-white focus:outline-none">
            Layanan
            <svg class="ml-1 h-5 w-5 text-gray-300 inline-flex" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 19l-7-7h14l-7 7zm0-9l-7-7h14l-7 7z" clip-rule="evenodd" />
            </svg>
          </button>
          <div class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none hidden">
            <div class="py-1">
              <a href="#" class="block px-4 py-2 text-base text-gray-700 hover:bg-gray-100">Layanan 1</a>
              <a href="#" class="block px-4 py-2 text-base text-gray-700 hover:bg-gray-100">Layanan 2</a>
              <a href="#" class="block px-4 py-2 text-base text-gray-700 hover:bg-gray-100">Layanan 3</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- Contoh Konten -->
  <div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Contoh Konten</h1>
    <p>Ini adalah contoh konten dari halaman web Anda.</p>
  </div>

  <script>
    // Script untuk mengaktifkan fungsi dropdown pada mobile menu
    const btnDropdown = document.querySelector('.sm\\:hidden .relative');
    const dropdown = document.querySelector('.sm\\:hidden .absolute');

    btnDropdown.addEventListener('click', function () {
      dropdown.classList.toggle('hidden');
    });
  </script>
</body>

</html>
