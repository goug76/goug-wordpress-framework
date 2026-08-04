<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * GOUG Admin UI showcase.
 *
 * @var string $heading
 * @var string $message
 */
?>

<div class="wrap goug-admin">
    <div class="goug-admin__page">
        <div class="goug-admin__content">

            <p class="goug-admin__eyebrow">
                Shared Admin UI
            </p>

            <h1 class="goug-admin__heading">
                <?php echo esc_html($heading); ?>
            </h1>

            <p class="goug-admin__muted">
                <?php echo esc_html($message); ?>
            </p>

            <section
                class="goug-admin__surface"
                style="margin-top: var(--goug-admin-space-6); padding: var(--goug-admin-space-6);"
            >
                <h2 class="goug-admin__heading">
                    Interface Primitives
                </h2>

                <p>
                    This page confirms that framework modules can
                    consume the shared Goug Labs administration
                    design system.
                </p>

                <div
                    style="
                        display: flex;
                        flex-wrap: wrap;
                        gap: var(--goug-admin-space-3);
                        margin-top: var(--goug-admin-space-4);
                    "
                >
                    <button
                        class="goug-admin__button"
                        type="button"
                    >
                        Primary Button
                    </button>

                    <button
                        class="goug-admin__button goug-admin__button--accent"
                        type="button"
                    >
                        Accent Button
                    </button>

                    <button
                        class="goug-admin__button goug-admin__button--secondary"
                        type="button"
                    >
                        Secondary Button
                    </button>
                </div>
            </section>

            <section
                class="goug-admin__surface"
                style="margin-top: var(--goug-admin-space-6); padding: var(--goug-admin-space-6);"
            >
                <h2 class="goug-admin__heading">
                    Status Treatments
                </h2>

                <div
                    style="
                        display: flex;
                        flex-wrap: wrap;
                        gap: var(--goug-admin-space-3);
                    "
                >
                    <span class="goug-admin__status goug-admin__status--success">
                        Healthy
                    </span>

                    <span class="goug-admin__status goug-admin__status--info">
                        Information
                    </span>

                    <span class="goug-admin__status goug-admin__status--warning">
                        Attention
                    </span>

                    <span class="goug-admin__status goug-admin__status--danger">
                        Critical
                    </span>
                </div>
            </section>

        </div>
    </div>
</div>