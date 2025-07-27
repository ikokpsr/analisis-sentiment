<!--<nav class="bg-white shadow-lg">-->
<!--  <div class="max-w-6xl mx-auto px-4">-->
<!--    <div class="flex justify-between space-x-7">-->
      <!-- Website Logo -->
<!--      <a href="#" class="flex items-center py-4 px-2">-->
<!--        {{-- <img src="logo.png" alt="Logo" class="h-8 w-8 mr-2"> --}}-->
<!--        <span class="inline-flex p-2 text-xl font-bold tracking-wider text-black uppercase">Analisis Sentimen</span>-->
<!--      </a>-->
<!--        <div class="hidden md:flex items-center space-x-1">-->
<!--          <a href="/home" class="py-4 px-2 text-blue-500 border-b-4 border-blue-500 font-semibold ">Home</a>-->
<!--          <a href="{{ route('shopee-api.index') }}" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">API Integration</a>-->
<!--          <a href="{{ route('shopee.sentiment') }}" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Sentiment Analysis</a>-->
<!--          <a href="{{ route('shopee-insight.index') }}" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Insight</a>-->
          
<!--        </div>-->
<!--        <div class="hidden md:flex items-center space-x-3 ">-->
<!--          <a href="" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Profile</a>-->
<!--        </div>-->
<!--        <div class="md:hidden flex items-center">-->
<!--          <button class="outline-none mobile-menu-button">-->
<!--          <svg class=" w-6 h-6 text-gray-500 hover:text-blue-500 "-->
<!--            x-show="!showMenu"-->
<!--            fill="none"-->
<!--            stroke-linecap="round"-->
<!--            stroke-linejoin="round"-->
<!--            stroke-width="2"-->
<!--            viewBox="0 0 24 24"-->
<!--            stroke="currentColor"-->
<!--          >-->
<!--            <path d="M4 6h16M4 12h16M4 18h16"></path>-->
<!--          </svg>-->
<!--        </button>-->
<!--        </div>-->
<!--    </div>-->
<!--  </div>-->
<!--  <div class="hidden mobile-menu">-->
<!--    <ul class="">-->
<!--      <li class="active"><a href="/home" class="block text-sm px-2 py-4 text-white bg-blue-500 font-semibold">Home</a></li>-->
<!--      <li><a href="{{ route('shopee-api.index') }}" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">API Integration</a></li>-->
<!--      <li><a href="{{ route('shopee.sentiment') }}" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Sentiment Analysis</a></li>-->
<!--      <li><a href="{{ route('shopee-insight.index') }}" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Insight</a></li>-->
<!--    </ul>-->
<!--  </div>-->
<!--</nav>-->

<!--  <script>-->
<!--    const btn = document.querySelector("button.mobile-menu-button");-->
<!--    const menu = document.querySelector(".mobile-menu");-->

<!--    btn.addEventListener("click", () => {-->
<!--      menu.classList.toggle("hidden");-->
<!--    });-->
<!--  </script>-->
  
<!--  <script>-->
<!--    document.addEventListener('DOMContentLoaded', function () {-->
<!--      var dropdownToggle = document.querySelectorAll('.relative');-->

<!--      dropdownToggle.forEach(function (toggle) {-->
<!--        toggle.addEventListener('click', function () {-->
<!--          var dropdownMenu = this.querySelector('ul');-->
<!--          dropdownMenu.classList.toggle('hidden');-->
<!--        });-->
<!--      });-->
<!--    });-->
<!--  </script>-->
<!--  <script>-->
<!--  </script>-->
    <header class="bg-white">
      <nav class="flex justify-between items-center w-[92%] mx-auto">
        <div>
          <!-- <img class="w-16 cursor-pointer" src="https://weddingmu.online/icon/AnaSen.png" alt="..."> -->
          <span
            class="inline-flex p-4 text-xl font-bold text-black uppercase"
            >Insightify</span
          >
        </div>
        <div
          class="nav-links duration-500 md:static absolute bg-white md:min-h-fit min-h-[60vh] left-0 top-[-100%] md:w-auto w-full flex items-center px-5"
        >
          <ul
            class="flex md:flex-row flex-col md:items-center md:gap-[4vw] gap-8"
          >
            <li>
              <a class="hover:text-gray-500" href="/home">Home</a>
            </li>
            <!--<li>-->
            <!--  <a-->
            <!--    class="hover:text-gray-500"-->
            <!--    href="{{ route('shopee-api.index') }}"-->
            <!--    >API Integration</a-->
            <!--  >-->
            <!--</li>-->
            <li>
              <a
                class="hover:text-gray-500"
                href="{{ route('shopee.sentiment') }}"
                >Sentiment Analysis</a
              >
            </li>
            <li>
              <a
                class="hover:text-gray-500"
                href="{{ route('shopee-insight.index') }}"
                >Insight</a
              >
            </li>
          </ul>
        </div>
        <div class="flex items-center gap-6">
          <!-- <button class="bg-[#a6c1ee] text-white px-5 py-2 rounded-full hover:bg-[#87acec]">Sign in</button> -->
          <ion-icon
            onclick="onToggleMenu(this)"
            name="menu"
            class="text-3xl cursor-pointer md:hidden"
          ></ion-icon>
        </div>
      </nav>
    </header>
    <script>
      const navLinks = document.querySelector(".nav-links");
      function onToggleMenu(e) {
        e.name = e.name === "menu" ? "close" : "menu";
        navLinks.classList.toggle("top-[9%]");
      }
    </script>
      