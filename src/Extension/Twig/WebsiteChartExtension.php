<?php

declare(strict_types=1);

namespace App\Extension\Twig;

use App\Entity\Website;
use App\Repository\ConsentRepository;
use App\Repository\WebsiteHitRepository;
use CalendR\Calendar;
use CalendR\Period\Day;
use CalendR\Period\Month;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFunction;

final class WebsiteChartExtension extends AbstractExtension
{
    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly WebsiteHitRepository $websiteHitRepository,
        private readonly ConsentRepository $consentRepository,
        private readonly IntlExtension $intlExtension,
    ) {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('website_hit_chart_month', $this->getWebsiteHitChartMonth(...), ['needs_environment' => true]),
            new TwigFunction('website_hit_chart_day', $this->getWebsiteHitChartDay(...), ['needs_environment' => true]),
            new TwigFunction('website_co2_chart_month', $this->getWebsiteCo2ChartMonth(...), ['needs_environment' => true]),
        ];
    }

    public function getWebsiteHitChartMonth(Environment $env, Website $website): Chart
    {
        $hitsByMonth = $this->websiteHitRepository->getCountByWebsiteGroupedByMonthOnAYear($website);
        $consentsByMonth = $this->consentRepository->getCountByWebsiteGroupedByMonthOnAYear($website);

        /** @var Month[] $months */
        $months = $this->getMonthsLabels();
        $chart = $this->create0ScaledChart();
        $chart->setData(
            [
                'labels' => array_map(fn (Month $month) => $this->intlExtension->formatDate($env, $month->getBegin(), pattern: 'MMMM'), $months),
                'datasets' => [
                    [
                        'label' => 'Page vues par mois',
                        'borderColor' => '#bada55',
                        'data' => array_map(
                            fn (Month $month) => array_values(array_filter(
                                $hitsByMonth,
                                fn (array $hit) => str_pad((string) $hit['month'], 2, '0', STR_PAD_LEFT) === $month->format('m') && $hit['year'] === $month->format('Y'),
                            ))[0]['count'] ?? 0,
                            $months,
                        ),
                    ],
                    [
                        'label' => 'Consentements par mois',
                        'borderColor' => '#ba55da',
                        'data' => array_map(
                            fn (Month $month) => array_values(array_filter(
                                $consentsByMonth,
                                fn (array $consent) => str_pad((string) $consent['month'], 2, '0', STR_PAD_LEFT) === $month->format('m') && $consent['year'] === $month->format('Y'),
                            ))[0]['count'] ?? 0,
                            $months,
                        ),
                    ],
                ],
            ]
        );

        return $chart;
    }

    public function getWebsiteHitChartDay(Environment $env, Website $website): Chart
    {
        $hitsByday = $this->websiteHitRepository->getCountByWebsiteGroupedByDayOnAMonth($website);
        $consentsByday = $this->consentRepository->getCountByWebsiteGroupedByDayOnAMonth($website);

        /** @var Day[] $days */
        $days = array_reverse(iterator_to_array(
            (function () {
                $currentDay = new Calendar()->getDay((int) date('Y'), (int) date('m'), (int) date('d'));
                for ($i = 0; $i < 30; ++$i) {
                    yield $currentDay;

                    $currentDay = $currentDay->getPrevious();
                }
            })(),
        ));

        $chart = $this->create0ScaledChart();
        $chart->setData(
            [
                'labels' => array_map(fn (Day $day) => $this->intlExtension->formatDate($env, $day->getBegin(), pattern: 'dd/MM'), $days),
                'datasets' => [
                    [
                        'label' => 'Page vues par jour',
                        'borderColor' => '#bada55',
                        'data' => array_map(
                            fn (Day $day) => array_values(array_filter(
                                $hitsByday,
                                fn (array $hit) => str_pad((string) $hit['day'], 2, '0', STR_PAD_LEFT) === $day->format('d')
                                    && str_pad((string) $hit['month'], 2, '0', STR_PAD_LEFT) === $day->format('m')
                                    && $hit['year'] === $day->format('Y'),
                            ))[0]['count'] ?? 0,
                            $days,
                        ),
                    ],
                    [
                        'label' => 'Consentements par jour',
                        'borderColor' => '#ba55da',
                        'data' => array_map(
                            fn (Day $day) => array_values(array_filter(
                                $consentsByday,
                                fn (array $consent) => str_pad((string) $consent['day'], 2, '0', STR_PAD_LEFT) === $day->format('d')
                                    && str_pad((string) $consent['month'], 2, '0', STR_PAD_LEFT) === $day->format('m')
                                    && $consent['year'] === $day->format('Y'),
                            ))[0]['count'] ?? 0,
                            $days,
                        ),
                    ],
                ],
            ]
        );

        return $chart;
    }

    public function getWebsiteCo2ChartMonth(Environment $env, Website $website): ?Chart
    {
        $quantityOfCO2eqPerYear = $website->server?->quantityOfCO2eqPerYear;
        if (!$quantityOfCO2eqPerYear) {
            return null;
        }

        /** @var array<array{count: int, month: string|int, year: string|int}> $hitsByMonth */
        $hitsByMonth = $this->websiteHitRepository->getCountByWebsiteGroupedByMonthOnAYear($website);
        $totalHits = array_sum(array_map(fn (array $hit): int => $hit['count'], $hitsByMonth)) ?: 1;
        $co2ByMonth = array_map(
            fn (array $hit) => [...$hit, 'co2' => ($hit['count'] ?: 0) * $quantityOfCO2eqPerYear / $totalHits],
            $hitsByMonth,
        );

        /** @var Month[] $months */
        $months = $this->getMonthsLabels();
        $chart = $this->create0ScaledChart();
        $chart->setData(
            [
                'labels' => array_map(fn (Month $month) => $this->intlExtension->formatDate($env, $month->getBegin(), pattern: 'MMMM'), $months),
                'datasets' => [
                    [
                        'label' => 'Émissions de GES par mois (kg CO2eq)',
                        'borderColor' => '#bada55',
                        'data' => array_map(
                            fn (Month $month) => array_values(array_filter(
                                $co2ByMonth,
                                fn (array $hit) => str_pad((string) $hit['month'], 2, '0', STR_PAD_LEFT) === $month->format('m') && $hit['year'] === $month->format('Y'),
                            ))[0]['co2'] ?? 0,
                            $months,
                        ),
                    ],
                ],
            ]
        );

        return $chart;
    }

    public function getMonthsLabels(): array
    {
        return array_reverse(iterator_to_array(
            (function () {
                $currentMonth = new Calendar()->getMonth((int) date('Y'), (int) date('m'));
                for ($i = 0; $i < 12; ++$i) {
                    yield $currentMonth;

                    $currentMonth = $currentMonth->getPrevious();
                }
            })(),
        ));
    }

    public function create0ScaledChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ]);

        return $chart;
    }
}
