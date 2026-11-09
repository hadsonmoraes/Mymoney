<style>
    #page-loader {
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: grid;
        place-items: center;
        background: #fff;
        transition: opacity 150ms ease, visibility 150ms ease;
    }

    .dark #page-loader {
        background: #101828;
    }

    #page-loader.is-hidden {
        opacity: 0;
        pointer-events: none;
        visibility: hidden;
    }

    #page-loader svg {
        width: 2.5rem;
        height: 2.5rem;
        color: #465fff;
        animation: page-loader-spin 1s linear infinite;
    }

    @keyframes page-loader-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #page-loader,
        #page-loader svg {
            animation: none;
            transition: none;
        }
    }
</style>
