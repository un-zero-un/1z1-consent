<?php

declare(strict_types=1);

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class MonacoEditorField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param array{attrs?: array<string, string>, language?: string} $options
     */
    #[\Override]
    public static function new(string $propertyName, ?string $label = null, array $options = []): self
    {
        $attrs = [...($options['attrs'] ?? []), 'data-monaco-editor' => ''];

        if (isset($options['language'])) {
            $attrs['data-monaco-language'] = $options['language'];
        }

        return new self()
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(TextareaType::class)
            ->addWebpackEncoreEntries('admin_editor')
            ->setFormTypeOption('attr', $attrs)
            ->addFormTheme('form/monaco.html.twig');
    }
}
