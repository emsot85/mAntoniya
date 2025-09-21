<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @stack('meta')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Filament + Livewire стили --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @filamentStyles
</head>

<body class="antialiased text-gray-800">

    <x-frontend-header />

    <main class="container mx-auto px-4 py-8 grid grid-cols-1 md:grid-cols-12 gap-6">
        <div class="md:col-span-9">
            @yield('content')
        </div>
        <aside class="md:col-span-3">
            {{-- Вставка виджетов --}}
            @include('partials.sidebar')
        </aside>
    </main>

     {{-- Сетка карточек --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $articles = \App\Models\Article::inRandomOrder()->limit(4)->get();
        @endphp
        @foreach($articles as $article)
        @php
        $currentLocale = app()->getLocale();
        $fallbackLocale = 'en';
        @endphp
        <div class="bg-white shadow rounded overflow-hidden">
        @if($article->image)
        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->translate($currentLocale)->title ?? $article->translate($fallbackLocale)->title }}" class="w-full h-40 object-cover">
        @endif
        <div class="p-4">
            <h3 class="font-bold text-lg">
               <a href="{{ route('articles.show', $article->slug) }}" class="mt-3 inline-block text-blue-600 hover:underline">
                {{ $article->translate($currentLocale)->title ?? $article->translate($fallbackLocale)->title }}
                </a>  
            </h3>
            <p class="text-gray-600 mt-2">
            {!! Str::limit(
                $article->translate($currentLocale)->description ??
                $article->translate($fallbackLocale)->description ??
                ($article->translate($currentLocale)->content ??
                $article->translate($fallbackLocale)->content),
                100
            ) !!}
            </p>
            <a href="{{ route('articles.show', $article->slug) }}" class="mt-3 inline-block text-blue-600 hover:underline">
            {{ __('messages.read_more') }}
            </a>
        </div>
        </div>
        @endforeach
    </div>

    <div class="p-4 bg-white flex justify-center shadow rounded">
        <a href="/articles"
            target="_blank"
            class="inline-block px-4 py-2 m-2 bg-amber-600 text-white font-semibold rounded-lg shadow hover:bg-amber-700 transition"
            >
             {{ __('messages.all_articles') }}
        </a>           
    </div>      


    {{-- Нижний блок (подвал над футером) --}}
    <section class="bg-gray-100 py-6">
        <div class="container mx-auto px-4">
            @yield('bottom')
        </div>
    </section>

    @if(\App\Models\BottomBlock::exists())

    <section class="bg-gray-100 py-6">
        <div class="container mx-auto px-4">
            <!-- Грид-контейнер с двумя колонками -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Первая колонка -->
                <div class="bg-white p-4 rounded-lg shadow">
                    {{-- Нижние блоки --}}
                    @foreach(\App\Models\BottomBlock::all() as $block)
                        <div class="mb-8">

                            {{-- Заголовок --}}
                            @if($block->translate()?->title)
                                <h2 class="text-xl font-bold mb-4">
                                    {{ $block->translate()?->title }}
                                </h2>
                            @endif

                            {{-- Картинка --}}
                            @if($block->translate()?->image)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $block->translate()?->image) }}" 
                                        alt="{{ $block->translate()?->title }}" 
                                        class="rounded-lg shadow">
                                </div>
                            @endif

                            {{-- Контент --}}
                            @if($block->translate()?->content)
                                <div class="prose max-w-none mb-4">
                                    {!! $block->translate()?->content !!}
                                </div>
                            @endif

                            @if($block->translate()?->extra_field_1)
                                <div class="prose max-w-none mb-4">
                                {!! $block->translate()?->extra_field_1 !!}
                                </div>
                            @endif

                            @if($block->translate()?->extra_field_2)
                                <div class="prose max-w-none mb-4">
                                    {!! $block->translate()?->extra_field_2 !!}
                                </div>
                            @endif

                            @if($block->translate()?->extra_field_3)
                                <div class="prose max-w-none mb-4">
                                    {!! $block->translate()?->extra_field_3 !!}
                                </div>
                            @endif

                            @if($block->translate()?->extra_field_4)
                                <div class="prose max-w-none mb-4">
                                    {!! $block->translate()?->extra_field_4 !!}
                                </div>
                            @endif
                        

                            {{-- Кнопки --}}
                            @php
                                $buttons = $block->translate()?->buttons 
                                    ? json_decode(json_encode($block->translate()?->buttons), true) 
                                    : [];
                            @endphp
                            @if(!empty($buttons))
                
                                <div class="flex flex-col gap-3">
                                    @foreach($buttons as $btn)
                                        <a href="{{ $btn['url'] }}"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                            {{ $btn['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    @endforeach

                </div>

                <!-- Вторая колонка -->
                <div class="bg-white p-4 rounded-lg shadow">
                   {{-- Нижние блоки --}}
                    @foreach(\App\Models\BottomBlock::all() as $block)
                        <div class="mb-8">
                        
                            {{-- Видео --}}
                            @php
                                $videos = $block->translate()?->videos 
                                    ? json_decode(json_encode($block->translate()?->videos), true) 
                                    : [];
                            @endphp
                            @if(!empty($videos))
                                <div class="space-y-4 mb-4">
                                    @foreach($videos as $video)
                                        @if($video['platform'] === 'youtube')
                                            <iframe class="w-full aspect-video rounded-lg"
                                                    src="https://www.youtube.com/embed/{{ \Illuminate\Support\Str::after($video['url'], 'v=') }}"
                                                    frameborder="0" allowfullscreen></iframe>
                                        @else
                                            <a href="{{ $video['url'] }}" 
                                            target="_blank" 
                                            class="text-blue-500 underline">
                                                {{ ucfirst($video['platform']) }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif


    {{-- Футер --}}
    <footer class="bg-gray-900 text-gray-300 py-6 px-4">
  
       <div class="mb-8">
            @php
                $footer = \App\Models\Footer::first()?->translate();
            @endphp

            {{-- Заголовок --}}
            @if($footer?->title)
                <h2 class="text-xl font-bold mb-4">
                    {!! $footer->title !!}
                </h2>
            @endif

            {{-- Контент --}}
            @if($footer?->content)
                <div class="prose max-w-none mb-8">
                    {!! $footer->content !!}
                </div>
            @endif

            {{-- Дополнительные поля в гриде --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach (['extra_field_1', 'extra_field_2', 'extra_field_3', 'extra_field_4'] as $field)
                    @if($footer?->$field)
                        <div class="prose max-w-none">
                            {!! $footer->$field !!}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>


        <div class="container mx-auto flex justify-between">
            <span>&copy; {{ date('Y') }} {{ __('messages.mother') }}</span>
            @include('partials.footer')
        </div>
    </footer>

    <div class="video-widget" data-state="default">
        <div class="video-widget__container">
            <video id="video-widget__video" loop="" autoplay="" playsinline="" preload="auto" muted="muted" controlslist="nodownload" disablepictureinpicture="" class="video-widget__video" src="https://mantoniya.ru/wp-content/uploads/2024/07/ma.mp4">
                <source src="https://mantoniya.ru/wp-content/uploads/2024/07/ma.mp4" type="video/mp4">
            </video>
            <div class="video-widget__close"></div>
            <a class="video-widget__button" href="https://cloud.mail.ru/public/fGTs/BYvproCCV" target="_blank">Скачать листовку</a>
        </div>
    </div>

    <style>
        .video-widget{position:fixed;left:0;z-index:999999;bottom:0}.video-widget__container{font-family:Helvetica;z-index:999999;overflow:hidden;border-style:solid;background:#eee;-webkit-transition:width .3s ease-in-out 0s,height .3s ease-in-out 0s,bottom .3s ease-in-out 0s,border-color .2s ease-in-out 0s,opacity 1s ease-in-out 0s,-webkit-transform .2s ease-in-out 0s;transition:width .3s ease-in-out 0s,height .3s ease-in-out 0s,bottom .3s ease-in-out 0s,border-color .2s ease-in-out 0s,opacity 1s ease-in-out 0s,-webkit-transform .2s ease-in-out 0s;-o-transition:width .3s ease-in-out 0s,height .3s ease-in-out 0s,bottom .3s ease-in-out 0s,border-color .2s ease-in-out 0s,opacity 1s ease-in-out 0s,-o-transform .2s ease-in-out 0s;-moz-transition:transform .2s ease-in-out 0s,width .3s ease-in-out 0s,height .3s ease-in-out 0s,bottom .3s ease-in-out 0s,border-color .2s ease-in-out 0s,opacity 1s ease-in-out 0s,-moz-transform .2s ease-in-out 0s;transition:transform .2s ease-in-out 0s,width .3s ease-in-out 0s,height .3s ease-in-out 0s,bottom .3s ease-in-out 0s,border-color .2s ease-in-out 0s,opacity 1s ease-in-out 0s;transition:transform .2s ease-in-out 0s,width .3s ease-in-out 0s,height .3s ease-in-out 0s,bottom .3s ease-in-out 0s,border-color .2s ease-in-out 0s,opacity 1s ease-in-out 0s,-webkit-transform .2s ease-in-out 0s,-moz-transform .2s ease-in-out 0s,-o-transform .2s ease-in-out 0s;outline:0;cursor:pointer;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-tap-highlight-color:transparent;-webkit-box-shadow:rgba(0,0,0,.2) 0 10px 20px;box-shadow:rgba(0,0,0,.2) 0 10px 20px;position:absolute;left:20px;bottom:50px;border-radius:20px;border-width:5px;width:130px;height:180px;border-color:#fff}.video-widget__container:hover{-webkit-transform:scale(1.05) translate(5px,-5px);-moz-transform:scale(1.05) translate(5px,-5px);-ms-transform:scale(1.05) translate(5px,-5px);-o-transform:scale(1.05) translate(5px,-5px);transform:scale(1.05) translate(5px,-5px);border-color:#131344}.video-widget__video{-o-object-fit:cover;object-fit:cover;position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);-moz-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);-o-transform:translate(-50%,-50%);transform:translate(-50%,-50%);width:100%;height:100%;min-width:100%;min-height:100%;z-index:200;-webkit-transition:opacity .4s ease-in-out 0s;-o-transition:opacity .4s ease-in-out 0s;-moz-transition:opacity .4s ease-in-out 0s;transition:opacity .4s ease-in-out 0s;opacity:.9}.video-widget__close{position:absolute;top:6px;right:6px;width:20px;height:20px;z-index:250;opacity:0;-webkit-transition:opacity .2s ease-in-out 0s,-webkit-transform .3s ease-in-out 0s;transition:opacity .2s ease-in-out 0s,-webkit-transform .3s ease-in-out 0s;-o-transition:opacity .2s ease-in-out 0s,-o-transform .3s ease-in-out 0s;-moz-transition:transform .3s ease-in-out 0s,opacity .2s ease-in-out 0s,-moz-transform .3s ease-in-out 0s;transition:transform .3s ease-in-out 0s,opacity .2s ease-in-out 0s;transition:transform .3s ease-in-out 0s,opacity .2s ease-in-out 0s,-webkit-transform .3s ease-in-out 0s,-moz-transform .3s ease-in-out 0s,-o-transform .3s ease-in-out 0s}.video-widget__close:after,.video-widget__close:before{position:absolute;left:9px;top:1px;content:" ";height:18px;width:2px;background:#fff;-webkit-box-shadow:rgba(0,0,0,.5) 1px 1px 10px;box-shadow:rgba(0,0,0,.5) 1px 1px 10px}.video-widget__close:before{-webkit-transform:rotate(45deg);-moz-transform:rotate(45deg);-ms-transform:rotate(45deg);-o-transform:rotate(45deg);transform:rotate(45deg)}.video-widget__close:after{-webkit-transform:rotate(-45deg);-moz-transform:rotate(-45deg);-ms-transform:rotate(-45deg);-o-transform:rotate(-45deg);transform:rotate(-45deg)}.video-widget__container:hover .video-widget__close{opacity:.5}.video-widget.video-widget[data-state=opened] .video-widget__container{width:280px;height:500px;border-radius:20px;border-color:#fff}.video-widget.video-widget[data-state=opened] .video-widget__close{opacity:.5}.video-widget.video-widget[data-state=opened] .video-widget__close:before{display:none}.video-widget.video-widget[data-state=opened] .video-widget__close:after{-webkit-transform:rotate(90deg);-moz-transform:rotate(90deg);-ms-transform:rotate(90deg);-o-transform:rotate(90deg);transform:rotate(90deg)}.video-widget.video-widget[data-state=opened] .video-widget__close:hover{opacity:1}.video-widget__button{position:absolute;bottom:20px;right:20px;left:20px;height:65px;border-radius:10px;z-index:300;-webkit-box-shadow:rgba(0,0,0,.25) 0 4px 15px;box-shadow:rgba(0,0,0,.25) 0 4px 15px;text-align:center;-webkit-transition:opacity .3s ease-in-out 0s,background-color .2s ease-in-out 0s,-webkit-transform .2s ease-in-out 0s;transition:opacity .3s ease-in-out 0s,background-color .2s ease-in-out 0s,-webkit-transform .2s ease-in-out 0s;-o-transition:opacity .3s ease-in-out 0s,background-color .2s ease-in-out 0s,-o-transform .2s ease-in-out 0s;-moz-transition:transform .2s ease-in-out 0s,opacity .3s ease-in-out 0s,background-color .2s ease-in-out 0s,-moz-transform .2s ease-in-out 0s;transition:transform .2s ease-in-out 0s,opacity .3s ease-in-out 0s,background-color .2s ease-in-out 0s;transition:transform .2s ease-in-out 0s,opacity .3s ease-in-out 0s,background-color .2s ease-in-out 0s,-webkit-transform .2s ease-in-out 0s,-moz-transform .2s ease-in-out 0s,-o-transform .2s ease-in-out 0s;visibility:hidden;background-color:#fdd82a;font-size:14px;font-family:Helvetica;color:#000;text-align:center;vertical-align:middle;line-height:65px;text-transform:uppercase;opacity:0}.video-widget__button:hover{background-color:#ffe257;text-decoration:none}.video-widget.video-widget[data-state=opened] .video-widget__button{opacity:1;visibility:visible}@media only screen and (max-width:1023px){.video-widget__close{opacity:.5}}@media only screen and (max-width:479px){.video-widget__container{left:15px;bottom:15px;width:90px;height:125px}}
        .video-widget__button  {color: #000000 !important; text-decoration: none;}
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const widget = document.querySelector(".video-widget");
        const video = document.getElementById("video-widget__video");
        const closeBtn = document.querySelector(".video-widget__close");
        const container = document.querySelector(".video-widget__container");

        if (!widget || !video) return;

        // Функция переключения состояния
        function toggleWidget() {
            if (widget.getAttribute("data-state") === "default") {
                widget.setAttribute("data-state", "opened");
                video.currentTime = 0;
                video.muted = false;
            } else {
                widget.setAttribute("data-state", "default");
                video.muted = true;
            }
        }

        // Клик по контейнеру
        container?.addEventListener("click", toggleWidget);

        // Touch для > 1024
        if (document.documentElement.clientWidth > 1024) {
            container?.addEventListener("touchstart", toggleWidget);
        }

        // Клик вне виджета
        document.addEventListener("mouseup", function (e) {
            if (!widget.contains(e.target) && widget.getAttribute("data-state") !== "default") {
                widget.setAttribute("data-state", "default");
                video.muted = true;
            }
        });

        // Клик на крестик — просто скрываем
        closeBtn?.addEventListener("click", function (e) {
            e.preventDefault();
            widget.style.display = "none";
            video.pause();
        });
    });
    </script>



	

	<div class="tg" data-v-46bc59e4>
		<a href="https://t.me/smAntoniya" target="_blank" class="tg_link" data-v-46bc59e4>
			<svg data-v-46bc59e4 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Livello_1" x="0px" y="0px" viewBox="0 0 240.1 240.1" enable-background="new 0 0 240.1 240.1" xml:space="preserve">
				<g id="Artboard">
						<linearGradient id="Oval_1_" gradientUnits="userSpaceOnUse" x1="-838.041" y1="660.581" x2="-838.041" y2="660.3427" gradientTransform="matrix(1000 0 0 -1000 838161 660581)">
						<stop offset="0" style="stop-color:#2AABEE"/>
						<stop offset="1" style="stop-color:#229ED9"/>
					</linearGradient>
					<circle id="Oval" fill-rule="evenodd" clip-rule="evenodd" fill="url(#Oval_1_)" cx="120.1" cy="120.1" r="120.1"/>
					<path id="Path-3" fill-rule="evenodd" clip-rule="evenodd" fill="#FFFFFF" d="M54.3,118.8c35-15.2,58.3-25.3,70-30.2   c33.3-13.9,40.3-16.3,44.8-16.4c1,0,3.2,0.2,4.7,1.4c1.2,1,1.5,2.3,1.7,3.3s0.4,3.1,0.2,4.7c-1.8,19-9.6,65.1-13.6,86.3   c-1.7,9-5,12-8.2,12.3c-7,0.6-12.3-4.6-19-9c-10.6-6.9-16.5-11.2-26.8-18c-11.9-7.8-4.2-12.1,2.6-19.1c1.8-1.8,32.5-29.8,33.1-32.3   c0.1-0.3,0.1-1.5-0.6-2.1c-0.7-0.6-1.7-0.4-2.5-0.2c-1.1,0.2-17.9,11.4-50.6,33.5c-4.8,3.3-9.1,4.9-13,4.8   c-4.3-0.1-12.5-2.4-18.7-4.4c-7.5-2.4-13.5-3.7-13-7.9C45.7,123.3,48.7,121.1,54.3,118.8z"/>
				</g>
				</svg>
		</a>
	</div>

    <style>
        img, svg {vertical-align: middle;}
        
        .tg[data-v-46bc59e4] {
            display: flex;
            flex-direction: column;
            align-items: center;
            align-content: center;
            justify-content: center;
            position: fixed;
            z-index: 999;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        .tg .tg_link[data-v-46bc59e4] {
            display: block;
            position: relative;
            width: 50px;
            height: 50px;
            border: 2px solid #fff;
            border-radius: 25px;
            transition: all .15s ease-in-out;
        }

        .tg .tg_link[data-v-46bc59e4]::after {
            content: "1";
            display: absolute;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            background: red;
            position: absolute;
            top: -7px;
            right: -7px;
            border-radius: 11px;
            color: #fff;
            text-align: center;
            font-size: 14px;
            line-height: 22px;
        }
    </style>


    {{-- Скрипты --}}
    @livewireScripts
    @filamentScripts
</body>

</html>