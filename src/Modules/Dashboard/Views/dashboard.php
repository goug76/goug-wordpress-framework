<?php

declare(strict_types=1);

defined('ABSPATH') || exit;
?>

<div class="wrap goug-admin goug-dashboard">
    <div class="goug-admin__page">
        <div class="goug-admin__content">

            <header class="goug-dashboard__header">
                <div class="goug-dashboard__intro">
                    <p class="goug-admin__eyebrow">
                        GOUG Framework
                    </p>

                    <h1 class="goug-admin__heading goug-dashboard__title">
                        Dashboard
                    </h1>

                    <p class="goug-admin__muted goug-dashboard__description">
                        A clearer view of your website, its health,
                        and the work that needs your attention.
                    </p>
                </div>

                <div class="goug-dashboard__actions">
                    <a
                        class="goug-admin__button goug-admin__button--secondary"
                        href="<?php echo esc_url(home_url('/')); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        View Site
                    </a>
                </div>
            </header>

            <main class="goug-dashboard__main">
                <section class="goug-admin__surface goug-dashboard__workspace">
                    <p class="goug-admin__eyebrow">
                        Dashboard Workspace
                    </p>

                    <h2 class="goug-admin__heading">
                        Your dashboard is ready for panels
                    </h2>

                    <p class="goug-admin__muted">
                        The page shell, shared design system, and
                        Dashboard-owned assets are now connected.
                    </p>
                </section>
            </main>

        </div>
    </div>
</div>