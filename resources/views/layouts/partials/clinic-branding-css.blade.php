<style>
    :root {
        --clinic-primary: {{ $clinicPrimaryColor ?? '#1e3a8a' }};
        --clinic-secondary: {{ $clinicSecondaryColor ?? '#f97316' }};
        --clinic-sidebar-text: {{ $clinicSidebarTextColor ?? '#ffffff' }};
    }
    .nav-active {
        background-color: var(--clinic-secondary) !important;
        color: white !important;
    }
</style>
