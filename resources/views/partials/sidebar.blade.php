<div class="space-y-4">
    <div class="p-4 bg-white  ">
        <a href="{{ asset('storage/docs/skachat-tekst-pravilo-matushki-antonii-dlya-skachivaniya.docx') }}"
            target="_blank"
            class="inline-block px-4 py-2 m-2 bg-amber-600 text-white font-semibold rounded-lg shadow hover:bg-amber-700 transition"
            download>
            📥 {{ __('messages.download_rule') }}
        </a>

        <a href="{{ asset('storage/docs/sbornik_svidetelstv_po_pravilu_m_antonii.docx') }}"
            target="_blank"
            class="inline-block px-4 m-2 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition"
            download>
            📥 {{ __('messages.download_all_testimonies') }}
        </a>
    </div>
    <div class="p-4 bg-white  ">

        <img
            src="{{ asset('storage/images/bp.jpeg') }}"
            alt=""
            class="h-auto w-auto mb-2 rounded-[1vw]"
        >

         <img
            src="{{ asset('storage/images/iisus-i-mladenec.jpg') }}"
            alt=""
            class="h-auto w-auto mb-2 rounded-[1vw]"
        >

         <img
            src="{{ asset('storage/images/02.jpg') }}"
            alt=""
            class="h-auto w-auto mb-2 rounded-[1vw]"
        >

         <img
            src="{{ asset('storage/images/bogorodica.jpg') }}"
            alt=""
            class="h-auto w-auto mb-2 rounded-[1vw]"
        >

    </div>
</div>