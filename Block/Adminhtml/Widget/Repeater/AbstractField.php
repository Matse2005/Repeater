<?php

declare(strict_types=1);

namespace MatseVH\Repeater\Block\Adminhtml\Widget\Repeater;

use Magento\Framework\Escaper;

abstract class AbstractField
{
    protected const SHOW_LABEL = true;
    protected static Escaper $escaper;

    public function __construct(
        protected string $name = '',
        protected string $label = '',
        protected bool $required = false,
        protected array $attributes = [],
    ) {
    }

    public function render(string $elementId): array
    {
        $templatePath = $this->getTemplatePath();

        if ($templatePath !== null) {
            return ['type' => 'template', 'value' => $templatePath];
        }

        return [
            'type' => 'html',
            'value' => $this->renderHtml($elementId),
        ];
    }

    public function renderHtml(string $elementId): string
    {
        return '';
    }

    public function getTemplatePath(): ?string
    {
        return null;
    }

    public function renderLabelHtml(string $elementId): string
    {
        if (!static::SHOW_LABEL) {
            return '';
        }

        return sprintf(
            '<label class="repeater-group__label" for="%s_%s_{{idx}}">
                    <span>%s</span>
            </label>',
            static::escaper()->escapeHtmlAttr($elementId),
            static::escaper()->escapeHtmlAttr($this->name),
            static::escaper()->escapeHtml($this->label),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function showLabel(): bool
    {
        return static::SHOW_LABEL;
    }

    public function getData(string $key = ''): mixed
    {
        if ($key === '') {
            return array_merge($this->attributes, [
                'name' => $this->name,
                'label' => $this->label,
                'required' => $this->required,
            ]);
        }

        return match ($key) {
            'name' => $this->name,
            'label' => $this->label,
            'required' => $this->required,
            default => $this->attributes[$key] ?? null,
        };
    }

    public function setData(string $key, mixed $value): static
    {
        match ($key) {
            'name' => $this->name = $value,
            'label' => $this->label = $value,
            'required' => $this->required = $value,
            default => $this->attributes[$key] = $value,
        };

        return $this;
    }

    public static function setEscaper(Escaper $escaper): void
    {
        static::$escaper = $escaper;
    }

    protected static function escaper(): Escaper
    {
        if (!isset(static::$escaper)) {
            throw new \RuntimeException(
                'Escaper has not been set on ' . static::class . '. Call AbstractField::setEscaper() first.'
            );
        }

        return static::$escaper;
    }
}
