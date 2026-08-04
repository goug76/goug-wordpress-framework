<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Dashboard page.
 *
 * @var list<\Goug\Framework\Modules\Dashboard\Models\Panel> $panels
 */
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
                <div class="goug-dashboard__grid">

                    <?php foreach ($panels as $panel) : ?>
                        <section
                            class="goug-admin__surface goug-dashboard__panel"
                            data-panel-id="<?php echo esc_attr($panel->id()); ?>"
                        >
                            <header class="goug-dashboard__panel-header">
                                <div>
                                    <h2 class="goug-admin__heading goug-dashboard__panel-title">
                                        <?php echo esc_html($panel->title()); ?>
                                    </h2>

                                    <?php if ($panel->description() !== '') : ?>
                                        <p class="goug-admin__muted goug-dashboard__panel-description">
                                            <?php echo esc_html($panel->description()); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </header>

                            <div class="goug-dashboard__panel-body">
                                <?php
                                $panelViewPath = $panel->viewPath();

                                if (! is_file($panelViewPath)) {
                                    throw new RuntimeException(
                                        sprintf(
                                            'The Dashboard panel view could not be found at "%s".',
                                            $panelViewPath
                                        )
                                    );
                                }

                                extract(
                                    $panel->viewData(),
                                    EXTR_SKIP
                                );

                                require $panelViewPath;
                                ?>
                            </div>
                        </section>
                    <?php endforeach; ?>

                </div>
            </main>

        </div>
    </div>
</div>