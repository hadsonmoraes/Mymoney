<div id="page-loader" class="page-loader fixed inset-0 z-[999999] flex items-center justify-center bg-white dark:bg-gray-900"
    role="status" aria-label="Carregando aplicação">
    <svg class="size-10 animate-spin text-brand-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 12a9 9 0 11-6.219-8.56"></path>
    </svg>
    <span class="sr-only">Carregando...</span>
</div>

<script>
    (() => {
        const pageLoader = document.getElementById('page-loader');
        const activeTransitions = new Set();
        let pageLoaded = false;

        const transitionKey = (event) => `${event.target.id}:${event.propertyName}`;
        const isLayoutElement = (element) => ['sidebar', 'app-content'].includes(element.id);
        const hidePageLoader = () => pageLoader?.classList.add('is-hidden');

        const finishTransition = (event) => {
            if (!isLayoutElement(event.target)) {
                return;
            }

            activeTransitions.delete(transitionKey(event));
            if (pageLoaded && activeTransitions.size === 0) {
                hidePageLoader();
            }
        };

        document.addEventListener('transitionrun', (event) => {
            if (isLayoutElement(event.target)) {
                activeTransitions.add(transitionKey(event));
            }
        }, true);
        document.addEventListener('transitionend', finishTransition, true);
        document.addEventListener('transitioncancel', finishTransition, true);

        const waitForLayoutTransitions = () => {
            pageLoaded = true;

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    if (activeTransitions.size === 0) {
                        hidePageLoader();
                    }
                });
            });

            window.setTimeout(hidePageLoader, 1000);
        };

        if (document.readyState === 'complete') {
            waitForLayoutTransitions();
        } else {
            window.addEventListener('load', waitForLayoutTransitions, { once: true });
        }
    })();
</script>
