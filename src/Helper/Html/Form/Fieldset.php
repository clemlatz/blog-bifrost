<?php
/**
 * @class Fieldset
 * @brief HTML Forms fieldset creation helpers
 *
 * @package Dotclear
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Helper\Html\Form;

class Fieldset extends Component
{
    private const DEFAULT_ELEMENT = 'fieldset';

    /**
     * Constructs a new instance.
     *
     * @param      mixed   $id       The identifier
     * @param      string  $element  The element
     */
    public function __construct($id = null, ?string $element = null)
    {
        parent::__construct(self::class, $element ?? self::DEFAULT_ELEMENT);
        if ($id !== null) {
            $this->setIdentifier($id);
        }
    }

    /**
     * Attaches the legend to this fieldset.
     *
     * @param      Legend|null  $legend  The legend
     */
    public function attachLegend(?Legend $legend)
    {
        if ($legend) {
            $this->legend($legend);
        } elseif (isset($this->legend)) {
            unset($this->legend);
        }
    }

    /**
     * Detaches the legend.
     */
    public function detachLegend()
    {
        if (isset($this->legend)) {
            unset($this->legend);
        }
    }

    /**
     * Renders the HTML component (including the associated legend if any).
     *
     * @return     string
     */
    public function render(): string
    {
        $buffer = '<' . ($this->getElement() ?? self::DEFAULT_ELEMENT) . $this->renderCommonAttributes() . '>' . "\n";

        if (isset($this->legend)) {
            $buffer .= $this->legend->render();
        }

        if (isset($this->fields) && is_array($this->fields)) {
            foreach ($this->fields as $field) {
                if (isset($this->legend) && $field->getDefaultElement() === 'legend') {
                    // Do not put more than one legend in fieldset
                    continue;
                }
                $buffer .= $field->render() . "\n";
            }
        }

        $buffer .= '</' . ($this->getElement() ?? self::DEFAULT_ELEMENT) . '>' . "\n";

        return $buffer;
    }

    /**
     * Gets the default element.
     *
     * @return     string  The default element.
     */
    public function getDefaultElement(): string
    {
        return self::DEFAULT_ELEMENT;
    }
}
