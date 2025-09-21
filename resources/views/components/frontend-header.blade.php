{{-- resources/views/components/frontend-header.blade.php --}}
<header x-data="{ mobileMenuOpen: false }" class="bg-white shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ">
    <div class="flex justify-between h-16 items-center">

      {{-- Логотип --}}
      <div class="flex-shrink-0">
        <a href="{{ url('/') }}" class="text-xl flex justify-between items-center font-bold text-primary-600">
          <img
            src="{{ asset('storage/images/logo/unnamed.jpg') }}"
            alt="Логотип"
            class="h-10 w-auto rounded-full"
          >
           <span class="px-4">{{ __('messages.mother') }}</span>
        </a>
      </div>


      @php
      $menu = \App\Http\Controllers\MenuController::getMenu('main');
      @endphp

      @if($menu)
      @foreach($menu->items as $item)
      <div class="relative group  hidden md:block">
        <a href="{{ $item->url ?? ($item->page?->slug ? route('page.show', $item->page->slug) : '#') }}"
          class="text-gray-700 hover:text-primary-600 font-medium">
          {{ $item->translate(app()->getLocale())->title ?? $item->title }}
        </a>

        {{-- Выпадающий список --}}
        @if($item->children->count())
        <div class="absolute left-0 mt-2 w-48 bg-white border rounded-lg shadow-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition duration-200">
          <ul class="py-2">
            @foreach($item->children as $child)
            <li>
              <a href="{{ $child->url ?? ($child->page?->slug ? route('page.show', $child->page->slug) : '#') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                {{ $child->translate(app()->getLocale())->title ?? $child->title }}
              </a>
            </li>
            @endforeach
          </ul>
        </div>
        @endif
      </div>
      @endforeach
      @endif
      </nav>

      {{-- Соцсети + бургер --}}
      <div class="flex items-center space-x-4">
        {{-- Соцсети (десктоп) --}}
        <div class="hidden md:flex space-x-4">
          <a href="https://t.me/smAntoniya" class="text-gray-500 hover:text-primary-600"  target="_blank">
           <svg fill="#228BE6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="30px" height="30px"><path d="M25,2c12.703,0,23,10.297,23,23S37.703,48,25,48S2,37.703,2,25S12.297,2,25,2z M32.934,34.375	c0.423-1.298,2.405-14.234,2.65-16.783c0.074-0.772-0.17-1.285-0.648-1.514c-0.578-0.278-1.434-0.139-2.427,0.219	c-1.362,0.491-18.774,7.884-19.78,8.312c-0.954,0.405-1.856,0.847-1.856,1.487c0,0.45,0.267,0.703,1.003,0.966	c0.766,0.273,2.695,0.858,3.834,1.172c1.097,0.303,2.346,0.04,3.046-0.395c0.742-0.461,9.305-6.191,9.92-6.693	c0.614-0.502,1.104,0.141,0.602,0.644c-0.502,0.502-6.38,6.207-7.155,6.997c-0.941,0.959-0.273,1.953,0.358,2.351	c0.721,0.454,5.906,3.932,6.687,4.49c0.781,0.558,1.573,0.811,2.298,0.811C32.191,36.439,32.573,35.484,32.934,34.375z"></path></svg>
          </a>
          <a href="https://dzen.ru/mantoniyaru?utm_referer=mantoniya.ru"  target="_blank" class="text-gray-500 hover:text-primary-600">
           <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 50 50" width="30px" height="30px"><path d="M46.894 23.986c.004 0 .007 0 .011 0 .279 0 .545-.117.734-.322.192-.208.287-.487.262-.769C46.897 11.852 38.154 3.106 27.11 2.1c-.28-.022-.562.069-.77.262-.208.192-.324.463-.321.746C26.193 17.784 28.129 23.781 46.894 23.986zM46.894 26.014c-18.765.205-20.7 6.202-20.874 20.878-.003.283.113.554.321.746.186.171.429.266.679.266.03 0 .061-.001.091-.004 11.044-1.006 19.787-9.751 20.79-20.795.025-.282-.069-.561-.262-.769C47.446 26.128 47.177 26.025 46.894 26.014zM22.823 2.105C11.814 3.14 3.099 11.884 2.1 22.897c-.025.282.069.561.262.769.189.205.456.321.734.321.004 0 .008 0 .012 0 18.703-.215 20.634-6.209 20.81-20.875.003-.283-.114-.555-.322-.747C23.386 2.173 23.105 2.079 22.823 2.105zM3.107 26.013c-.311-.035-.555.113-.746.321-.192.208-.287.487-.262.769.999 11.013 9.715 19.757 20.724 20.792.031.003.063.004.094.004.25 0 .492-.094.678-.265.208-.192.325-.464.322-.747C23.741 32.222 21.811 26.228 3.107 26.013z"/></svg>
          </a>        

          {{-- Переключатель языков --}}
          <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
              class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
              🌐 {{ strtoupper(app()->getLocale()) }}
              <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <div x-show="open"
              @click.away="open = false"
              x-transition
              class="absolute right-0 mt-2 w-24 bg-white border rounded shadow-lg z-50">
              @foreach(config('translatable.locales') as $locale)
              <a href="{{ route('locale.switch', $locale) }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                {{ strtoupper($locale) }}
              </a>
              @endforeach
            </div>
          </div>

        </div>

        {{-- Кнопка бургера (мобилка) --}}
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-primary-600 focus:outline-none">
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }" class="block" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  {{-- Мобильное меню --}}
  <div x-show="mobileMenuOpen" class="md:hidden bg-white border-t shadow-md">
    <nav class="px-4 py-4 space-y-2">
      @if($menu)
      @foreach($menu->items as $item)
      <div>
        <a href="{{ $item->url ?? ($item->page?->slug ? route('page.show', $item->page->slug) : '#') }}"
          class="block text-gray-700 hover:text-primary-600 font-medium">
          {{ $item->translate(app()->getLocale())->title ?? $item->title }}
        </a>

        {{-- Вложенные пункты --}}
        @if($item->children->count())
        <ul class="ml-4 mt-2 space-y-1">
          @foreach($item->children as $child)
          <li>
            <a href="{{ $child->url ?? ($child->page?->slug ? route('page.show', $child->page->slug) : '#') }}"
              class="block text-gray-600 hover:text-primary-600">
              {{ $child->translate(app()->getLocale())->title ?? $child->title }}
            </a>
          </li>
          @endforeach
        </ul>
        @endif
      </div>
      @endforeach
      @endif

      {{-- Соцсети (в мобилке) --}}
      <div class="flex space-x-4 mt-4">
        <a href="https://t.me/smAntoniya" class="text-gray-500 hover:text-primary-600" target="_blank">
           <svg fill="#228BE6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="30px" height="30px"><path d="M25,2c12.703,0,23,10.297,23,23S37.703,48,25,48S2,37.703,2,25S12.297,2,25,2z M32.934,34.375	c0.423-1.298,2.405-14.234,2.65-16.783c0.074-0.772-0.17-1.285-0.648-1.514c-0.578-0.278-1.434-0.139-2.427,0.219	c-1.362,0.491-18.774,7.884-19.78,8.312c-0.954,0.405-1.856,0.847-1.856,1.487c0,0.45,0.267,0.703,1.003,0.966	c0.766,0.273,2.695,0.858,3.834,1.172c1.097,0.303,2.346,0.04,3.046-0.395c0.742-0.461,9.305-6.191,9.92-6.693	c0.614-0.502,1.104,0.141,0.602,0.644c-0.502,0.502-6.38,6.207-7.155,6.997c-0.941,0.959-0.273,1.953,0.358,2.351	c0.721,0.454,5.906,3.932,6.687,4.49c0.781,0.558,1.573,0.811,2.298,0.811C32.191,36.439,32.573,35.484,32.934,34.375z"></path></svg>
          </a>
          <a href="https://dzen.ru/mantoniyaru?utm_referer=mantoniya.ru"  target="_blank" class="text-gray-500 hover:text-primary-600">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="30px" height="30px"><path d="M46.894 23.986c.004 0 .007 0 .011 0 .279 0 .545-.117.734-.322.192-.208.287-.487.262-.769C46.897 11.852 38.154 3.106 27.11 2.1c-.28-.022-.562.069-.77.262-.208.192-.324.463-.321.746C26.193 17.784 28.129 23.781 46.894 23.986zM46.894 26.014c-18.765.205-20.7 6.202-20.874 20.878-.003.283.113.554.321.746.186.171.429.266.679.266.03 0 .061-.001.091-.004 11.044-1.006 19.787-9.751 20.79-20.795.025-.282-.069-.561-.262-.769C47.446 26.128 47.177 26.025 46.894 26.014zM22.823 2.105C11.814 3.14 3.099 11.884 2.1 22.897c-.025.282.069.561.262.769.189.205.456.321.734.321.004 0 .008 0 .012 0 18.703-.215 20.634-6.209 20.81-20.875.003-.283-.114-.555-.322-.747C23.386 2.173 23.105 2.079 22.823 2.105zM3.107 26.013c-.311-.035-.555.113-.746.321-.192.208-.287.487-.262.769.999 11.013 9.715 19.757 20.724 20.792.031.003.063.004.094.004.25 0 .492-.094.678-.265.208-.192.325-.464.322-.747C23.741 32.222 21.811 26.228 3.107 26.013z"/></svg>
          </a>       

        {{-- Переключатель языков --}}
        <div x-data="{ open: false }" class="relative">
          <button @click="open = !open"
            class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
            🌐 {{ strtoupper(app()->getLocale()) }}
            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <div x-show="open"
            @click.away="open = false"
            x-transition
            class="absolute right-0 mt-2 w-24 bg-white border rounded shadow-lg z-50">
            @foreach(config('translatable.locales') as $locale)
            <a href="{{ route('locale.switch', $locale) }}"
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              {{ strtoupper($locale) }}
            </a>
            @endforeach
          </div>
        </div>

      </div>
    </nav>
  </div>
</header>