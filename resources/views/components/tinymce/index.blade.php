@props([
    'id',
    'options' => [
        'menubar' => false,
        'relative_urls' => false,
        'remove_script_host' => false,
        'convert_urls' => true,
    ],
    'toolbar' => [ 'styles | bold italic underline strikethrough | bullist numlist | alignleft aligncenter alignright alignjustify | link image codesample | code fullscreen' ],
    'plugins' => [
        'lists',
        'autolink',
        'fullscreen',
        'link',
        'image',
        'code',
        'table',
        'help',
        'codesample',
    ]
])

<textarea id="{{ $id }}" {{ $attributes }}></textarea>

@pushOnce('body')
    <script src="{{ asset('vendor/lazyui/vendor/tinymce/tinymce.min.js') }}"></script>
    <script>
        window.tinymceInstances = [];

        function __loadTinyMCE({ id, theme = 'light', options = {} }) {
            const selector = `#${id}`;

            return new Promise((resolve) => {
                if (tinymceInstances.find((item) => item?.id === id)) {
                    tinymce.get(id).destroy();
                } else {
                    tinymceInstances.push({ id, options });
                }


                tinymce.init({
                    selector,
                    license_key: 'gpl',
                    skin: `oxide${theme === 'dark' ? '-dark' : ''}`,
                    content_css: theme === 'dark' ? 'dark' : 'default',
                    ...options,
                    setup(editor) {
                        editor.on('change', function () {
                            tinymce.triggerSave();
                        });

                        editor.on('init', function () {
                            resolve(editor);
                        })
                    }
                });
            })
        }

        window.addEventListener(window.LazyUIConfig?.eventKey || 'lazy-appearance-change', (event) => {
            let theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            tinymceInstances.forEach((item) => {
                const { id, options } = item;
                __loadTinyMCE({ id, theme, options });
            })
        });
    </script>
@endPushOnce

@push('body')
    <script>
        window.lazyReady(() => {
            let theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            __loadTinyMCE({
                id: @js($id),
                theme,
                options: {
                    ...@json($options),
                    toolbar: @json($toolbar),
                    plugins: @json($plugins),
                }
            }).then((editor) => {
                editor.setContent(@js((string) $slot))
            })
        });
    </script>
@endpush
