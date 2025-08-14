<?php

namespace JobMetric\MultiCalendar\Tests\Helpers;

use JobMetric\MultiCalendar\Helpers\NumberTransliterator;
use PHPUnit\Framework\TestCase;

class NumberTransliteratorTest extends TestCase
{
    public function testAsciiToPersian(): void
    {
        $this->assertSame('۱۲۳٬۴۵۶٫۷۸', NumberTransliterator::trNum('123,456.78', 'fa'));
        $this->assertSame('۰', NumberTransliterator::trNum('0', 'fa'));
        $this->assertSame('۲۰۲۵/۰۸/۱۳', NumberTransliterator::trNum('2025/08/13', 'fa'));
    }

    public function testPersianToAscii(): void
    {
        $this->assertSame('123,456.78', NumberTransliterator::trNum('۱۲۳٬۴۵۶٫۷۸', 'en'));
        $this->assertSame('0', NumberTransliterator::trNum('۰', 'en'));
        $this->assertSame('2025/08/13', NumberTransliterator::trNum('۲۰۲۵/۰۸/۱۳', 'en'));
    }

    public function testArabicIndicToAscii(): void
    {
        $this->assertSame('1234567890', NumberTransliterator::trNum('١٢٣٤٥٦٧٨٩٠', 'en'));
        $this->assertSame('0.5', NumberTransliterator::trNum('٠٫٥', 'en'));
        $this->assertSame('1,234', NumberTransliterator::trNum('١٬٢٣٤', 'en'));
    }

    public function testIdempotency(): void
    {
        $s1 = NumberTransliterator::trNum('123,456.78', 'fa');
        $s2 = NumberTransliterator::trNum($s1, 'fa');
        $this->assertSame($s1, $s2);

        $e1 = NumberTransliterator::trNum('۱۲۳٬۴۵۶٫۷۸', 'en');
        $e2 = NumberTransliterator::trNum($e1, 'en');
        $this->assertSame($e1, $e2);
    }

    public function testMixedContent(): void
    {
        $in  = 'Ref# A-۱۲۳۴ on ۲۰۲۵/۰۸/۱۳ amount ١٬٢٣٤٫٥٦';
        $out = 'Ref# A-1234 on 2025/08/13 amount 1,234.56';
        $this->assertSame($out, NumberTransliterator::trNum($in, 'en'));

        $in2  = 'Ref# A-1234 on 2025/08/13 amount 1,234.56';
        $out2 = 'Ref# A-۱۲۳۴ on ۲۰۲۵/۰۸/۱۳ amount ۱٬۲۳۴٫۵۶';
        $this->assertSame($out2, NumberTransliterator::trNum($in2, 'fa'));
    }
}
