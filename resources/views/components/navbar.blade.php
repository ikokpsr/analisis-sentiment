<nav class="bg-white shadow-lg">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex justify-between space-x-7">
      <!-- Website Logo -->
      <a href="#" class="flex items-center py-4 px-2">
        {{-- <img src="logo.png" alt="Logo" class="h-8 w-8 mr-2"> --}}
        <span class="inline-flex p-2 text-xl font-bold tracking-wider text-black uppercase">Analisis Sentimen</span>
      </a>
        <div class="hidden md:flex items-center space-x-1">
          <a href="/home" class="py-4 px-2 text-blue-500 border-b-4 border-blue-500 font-semibold ">Home</a>
          <a href="{{ route('shopee-api.index') }}" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">API Integration</a>
          <a href="{{ route('shopee.sentiment') }}" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Sentiment Analysis</a>
          <a href="{{ route('shopee-insight.index') }}" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Insight</a>
          {{-- <div class="relative">
            <button type="button" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">
              Integrasi API
            </button>
            <ul class="absolute left-auto right-0 hidden mt-1 m-0 float-left space-y-2 bg-white border border-gray-300">
              <li>
                <a href="{{ route('instagram-api.index') }}" class="block w-full whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-white/30">Instagram</a>
              </li>
              <li>
                <a href="#" class="block w-full whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-white/30">Shopee</a>
              </li>
            </ul>
          </div> --}}

          {{-- <div class="relative">
            <a class="hidden-arrow flex items-center whitespace-nowrap py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-150 ease-in-out motion-reduce:transition-none" 
               href="#" role="button" id="dropdownMenu1" data-te-dropdown-toggle-ref aria-expanded="false">
              Integrasi API
            </a>
            <ul class="absolute left-0 z-[1000] float-left m-0 mt-1 w-36 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-left text-base shadow-lg dark:bg-neutral-700 [&[data-te-dropdown-show]]:block" aria-labelledby="dropdownMenu1" data-te-dropdown-menu-ref>
              <li>
                <a href="{{ route('instagram-api.index') }}" class="block w-full whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-white/30">
                  Instagram
                </a>
              </li>
              <li>
                <a href="{{ route('shopee-api.index') }}" class="block w-full whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-white/30">
                  Shopee
                </a>
              </li>
            </ul>
          </div> --}}

          {{-- <div class="relative">
            <button type="button" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">
              Sentiment Analysis
            </button>
            <ul class="absolute left-0 hidden mt-2 space-y-2 bg-white border border-gray-300">
              <li>
                <a href="{{ route('manual.input') }}" class="block py-2 px-4 text-gray-500 hover:text-blue-500">Manual Input</a>
              </li>
              <li>
                <a href="{{ route('instagram.sentiment') }}" class="block py-2 px-4 text-gray-500 hover:text-blue-500">Instagram</a>
              </li>
              <li>
                <a href="{{ route('shopee.sentiment') }}" class="block py-2 px-4 text-gray-500 hover:text-blue-500">Shopee</a>
              </li>
            </ul>
          </div> --}}

          {{-- <div class="relative">
            <button type="button" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">
              Insight
            </button>
            <ul class="absolute left-0 hidden mt-2 space-y-2 bg-white border border-gray-300">
              <li>
                <a href="{{ route('manual.input') }}" class="block py-2 px-4 text-gray-500 hover:text-blue-500">Instagram</a>
              </li>
              <li>
                <a href="{{ route('shopee-insight.index') }}" class="block py-2 px-4 text-gray-500 hover:text-blue-500">Shopee</a>
              </li>
            </ul>
          </div> --}}
          {{-- <a href="{{ route('data-latih.index') }}" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Training Dataset</a> --}}
        </div>
        <div class="hidden md:flex items-center space-x-3 ">
          <a href="" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Profile</a>
        </div>
        <div class="md:hidden flex items-center">
          <button class="outline-none mobile-menu-button">
          <svg class=" w-6 h-6 text-gray-500 hover:text-blue-500 "
            x-show="!showMenu"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        </div>
    </div>
  </div>
  <div class="hidden mobile-menu">
    <ul class="">
      <li class="active"><a href="/home" class="block text-sm px-2 py-4 text-white bg-green-500 font-semibold">Home</a></li>
      <li><a href="#services" class="block text-sm px-2 py-4 hover:bg-green-500 transition duration-300">Services</a></li>
      <li><a href="#about" class="block text-sm px-2 py-4 hover:bg-green-500 transition duration-300">About</a></li>
      <li><a href="#contact" class="block text-sm px-2 py-4 hover:bg-green-500 transition duration-300">Contact Us</a></li>
    </ul>
  </div>
</nav>

  <script>
    const btn = document.querySelector("button.mobile-menu-button");
    const menu = document.querySelector(".mobile-menu");

    btn.addEventListener("click", () => {
      menu.classList.toggle("hidden");
    });
  </script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var dropdownToggle = document.querySelectorAll('.relative');

      dropdownToggle.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
          var dropdownMenu = this.querySelector('ul');
          dropdownMenu.classList.toggle('hidden');
        });
      });
    });
  </script>
</nav>