<?php

namespace App\Support;

/**
 * Palmer notation renders a tooth as its position number (1-8) wrapped in
 * an L-shaped bracket that indicates the quadrant, e.g.:
 *
 *   Upper Right 8  ->  8⌐   (bracket opens down-left)
 *   Upper Left  8  ->  ¬8   (bracket opens down-right)
 *   Lower Right 8  ->  8_|  (visually: bracket opens up-left)
 *   Lower Left  8  ->  |_8  (visually: bracket opens up-right)
 *
 * True Palmer brackets are drawn as actual perpendicular lines in printed
 * charts. In HTML/plain text we approximate with Unicode box-drawing
 * corner characters, which is the standard approach used by digital
 * dental-charting software when a real vector bracket isn't practical.
 */
class PalmerNotation
{
    private const QUADRANT_LABELS = [
        'UR' => 'Upper Right',
        'UL' => 'Upper Left',
        'LL' => 'Lower Left',
        'LR' => 'Lower Right',
    ];

    // Real Palmer brackets are two perpendicular strokes (a vertical line
    // + a horizontal line) that visually enclose the number on the side
    // facing the midline. Plain text/Unicode has no single character that
    // draws a true right-angle bracket per quadrant, so we approximate
    // using the box-drawing corner set (┐┌┘└), which is what most
    // Unicode-based dental charting UIs use, rather than mixing in
    // unrelated characters like ⌐/¬/a bare letter (an earlier draft of
    // this did that inconsistently — e.g. Lower Left literally used the
    // letter "L", which reads as a typo, not a bracket).
    //
    //   UR: tooth number, then a corner opening down-left   -> "8┐"
    //   UL: a corner opening down-right, then tooth number  -> "┌8"
    //   LL: a corner opening up-right, then tooth number    -> "└8"
    //   LR: tooth number, then a corner opening up-left     -> "8┘"
    private const QUADRANT_CORNERS = [
        'UR' => ['before' => '', 'after' => '┐'],
        'UL' => ['before' => '┌', 'after' => ''],
        'LL' => ['before' => '└', 'after' => ''],
        'LR' => ['before' => '', 'after' => '┘'],
    ];

    public static function label(string $quadrant, int $position): string
    {
        if (!isset(self::QUADRANT_CORNERS[$quadrant])) {
            throw new \InvalidArgumentException('Unknown Palmer quadrant.');
        }

        if ($position < 1 || $position > 8) {
            throw new \InvalidArgumentException('Palmer tooth position must be between 1 and 8.');
        }

        $corner = self::QUADRANT_CORNERS[$quadrant];

        return "{$corner['before']}{$position}{$corner['after']}";
    }

    public static function quadrantName(string $quadrant): string
    {
        return self::QUADRANT_LABELS[$quadrant] ?? $quadrant;
    }

    public static function fullLabel(string $quadrant, int $position): string
    {
        return self::label($quadrant, $position).' — '.self::quadrantName($quadrant);
    }

    /**
     * Ordered list of quadrants for rendering the chart in the conventional
     * layout: upper row left-to-right (patient's right first, per dental
     * charting convention where the chart mirrors the patient facing you),
     * lower row the same.
     */
    public static function quadrantOrder(): array
    {
        return ['UR', 'UL', 'LL', 'LR'];
    }
}
