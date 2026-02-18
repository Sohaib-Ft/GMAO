<?php

namespace Tests\Unit;

use App\Services\RruleParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RruleParserTest extends TestCase
{
    /**
     * Test validation des RRULE valides
     */
    public function test_valid_rrule_formats()
    {
        $validRrules = [
            'FREQ=DAILY',
            'FREQ=DAILY;INTERVAL=2',
            'FREQ=WEEKLY;BYDAY=MO,WE,FR',
            'FREQ=WEEKLY;INTERVAL=2;BYDAY=TU,TH',
            'FREQ=MONTHLY;BYMONTHDAY=15',
            'FREQ=MONTHLY;BYDAY=1MO',
            'FREQ=YEARLY',
            'FREQ=YEARLY;INTERVAL=2',
        ];

        foreach ($validRrules as $rrule) {
            $this->assertTrue(
                RruleParser::isValidRrule($rrule),
                "RRULE '{$rrule}' should be valid"
            );
        }
    }

    /**
     * Test rejet des RRULE invalides
     */
    public function test_invalid_rrule_formats()
    {
        $invalidRrules = [
            '',
            'INVALID',
            'FREQ=INVALID',
            'FREQ=DAILY;BYDAY=INVALID',
            'FREQ=WEEKLY;BYDAY=',
            'FREQ=DAILY;INVALID=VALUE',
        ];

        foreach ($invalidRrules as $rrule) {
            $this->assertFalse(
                RruleParser::isValidRrule($rrule),
                "RRULE '{$rrule}' should be invalid"
            );
        }
    }

    /**
     * Test parsing de la fréquence
     */
    public function test_get_frequency()
    {
        $parser = new RruleParser('FREQ=WEEKLY;BYDAY=MO,WE,FR');
        $this->assertEquals('WEEKLY', $parser->getFrequency());

        $parser = new RruleParser('FREQ=DAILY;INTERVAL=2');
        $this->assertEquals('DAILY', $parser->getFrequency());
    }

    /**
     * Test parsing de l'intervalle
     */
    public function test_get_interval()
    {
        $parser = new RruleParser('FREQ=DAILY');
        $this->assertEquals(1, $parser->getInterval());

        $parser = new RruleParser('FREQ=DAILY;INTERVAL=5');
        $this->assertEquals(5, $parser->getInterval());
    }

    /**
     * Test parsing des jours de la semaine
     */
    public function test_get_weekdays()
    {
        $parser = new RruleParser('FREQ=WEEKLY;BYDAY=MO,WE,FR');
        $weekdays = $parser->getWeekdays();

        $this->assertContains('MO', $weekdays);
        $this->assertContains('WE', $weekdays);
        $this->assertContains('FR', $weekdays);
        $this->assertCount(3, $weekdays);
    }

    /**
     * Test parsing du jour du mois
     */
    public function test_get_day_of_month()
    {
        $parser = new RruleParser('FREQ=MONTHLY;BYMONTHDAY=15');
        $this->assertEquals(15, $parser->getDayOfMonth());

        $parser = new RruleParser('FREQ=WEEKLY;BYDAY=MO');
        $this->assertNull($parser->getDayOfMonth());
    }

    /**
     * Test résumé en français pour récurrence quotidienne
     */
    public function test_french_summary_daily()
    {
        $parser = new RruleParser('FREQ=DAILY');
        $this->assertStringContainsString('chaque jour', $parser->toFrench());

        $parser = new RruleParser('FREQ=DAILY;INTERVAL=2');
        $this->assertStringContainsString('tous les 2 jours', $parser->toFrench());
    }

    /**
     * Test résumé en français pour récurrence hebdomadaire
     */
    public function test_french_summary_weekly()
    {
        $parser = new RruleParser('FREQ=WEEKLY;BYDAY=MO,WE,FR');
        $summary = $parser->toFrench();

        $this->assertStringContainsString('chaque semaine', $summary);
        $this->assertStringContainsString('lundi', $summary);
        $this->assertStringContainsString('mercredi', $summary);
        $this->assertStringContainsString('vendredi', $summary);
    }

    /**
     * Test résumé en français pour récurrence mensuelle (jour fixe)
     */
    public function test_french_summary_monthly_fixed_day()
    {
        $parser = new RruleParser('FREQ=MONTHLY;BYMONTHDAY=1');
        $summary = $parser->toFrench();

        $this->assertStringContainsString('chaque mois', $summary);
        $this->assertStringContainsString('le 1er', $summary);

        $parser = new RruleParser('FREQ=MONTHLY;BYMONTHDAY=15');
        $summary = $parser->toFrench();
        $this->assertStringContainsString('le 15', $summary);
    }

    /**
     * Test résumé en français pour récurrence mensuelle (jour relatif)
     */
    public function test_french_summary_monthly_relative_day()
    {
        $parser = new RruleParser('FREQ=MONTHLY;BYDAY=1MO');
        $summary = $parser->toFrench();

        $this->assertStringContainsString('premier', $summary);
        $this->assertStringContainsString('lundi', $summary);
    }

    /**
     * Test construction de RRULE
     */
    public function test_build_rrule()
    {
        $rrule = RruleParser::build(
            frequency: 'WEEKLY',
            interval: 1,
            byday: ['MO', 'WE', 'FR']
        );

        $this->assertEquals('FREQ=WEEKLY;BYDAY=MO,WE,FR', $rrule);
        $this->assertTrue(RruleParser::isValidRrule($rrule));
    }

    /**
     * Test construction avec intervalle
     */
    public function test_build_rrule_with_interval()
    {
        $rrule = RruleParser::build(
            frequency: 'MONTHLY',
            interval: 3,
            bymonthday: 15
        );

        $this->assertStringContainsString('FREQ=MONTHLY', $rrule);
        $this->assertStringContainsString('INTERVAL=3', $rrule);
        $this->assertStringContainsString('BYMONTHDAY=15', $rrule);
    }

    /**
     * Test construction invalide
     */
    public function test_build_invalid_rrule_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);

        RruleParser::build(
            frequency: 'INVALID_FREQUENCY'
        );
    }

    /**
     * Test toString
     */
    public function test_to_string()
    {
        $rrule = 'FREQ=WEEKLY;BYDAY=MO,WE,FR';
        $parser = new RruleParser($rrule);

        $this->assertEquals($rrule, $parser->toString());
    }

    /**
     * Test getParts
     */
    public function test_get_parts()
    {
        $parser = new RruleParser('FREQ=WEEKLY;INTERVAL=2;BYDAY=MO,WE,FR');
        $parts = $parser->getParts();

        $this->assertEquals('WEEKLY', $parts['FREQ']);
        $this->assertEquals('2', $parts['INTERVAL']);
        $this->assertEquals('MO,WE,FR', $parts['BYDAY']);
    }

    /**
     * Test avec RRULE invalide lors de la construction
     */
    public function test_constructor_throws_on_invalid_rrule()
    {
        $this->expectException(InvalidArgumentException::class);

        new RruleParser('INVALID_FORMAT');
    }

    /**
     * Test nextOccurrences pour DAILY
     */
    public function test_next_occurrences_daily()
    {
        $parser = new RruleParser('FREQ=DAILY;INTERVAL=1');
        $startDate = new \DateTime('2024-01-01');
        $occurrences = $parser->getNextOccurrences($startDate, 3);

        $this->assertCount(3, $occurrences);
        $this->assertEquals('2024-01-02', $occurrences[0]->format('Y-m-d'));
        $this->assertEquals('2024-01-03', $occurrences[1]->format('Y-m-d'));
        $this->assertEquals('2024-01-04', $occurrences[2]->format('Y-m-d'));
    }

    /**
     * Test nextOccurrences pour WEEKLY
     */
    public function test_next_occurrences_weekly()
    {
        $parser = new RruleParser('FREQ=WEEKLY;BYDAY=MO');
        $startDate = new \DateTime('2024-01-01'); // Monday
        $occurrences = $parser->getNextOccurrences($startDate, 2);

        $this->assertCount(2, $occurrences);
        // Should be 2024-01-08 (next Monday) and 2024-01-15
        $this->assertEquals('Monday', $occurrences[0]->format('l'));
        $this->assertEquals('Monday', $occurrences[1]->format('l'));
    }
}
