<?php
declare(strict_types = 1);

namespace Controllers;

use BadRequest;
use Controller;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Unauthorized;

/**
 * Controller for statistics
 */
class StatisticController extends Controller
{
    /**
     * Return global statistics of the app
     * Usage: GET /statistics | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getGlobalStats(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Nb users
        $nb_users = (int) ($this->database()->find(
            "users",
            ["count(*) as count"],
            exception: false
        ))[0]["count"];

        // Nb share
        $nb_share = (int) ($this->database()->find(
            "users",
            ["sum(nb_share) as sum"],
            exception: false
        ))[0]["sum"];

        // Avg usage per days
        $avg_usage_day = (float) ($this->database()->find(
            "users",
            ["count(*) as count"],
            ["updated_at >=" => $this->getDate(strtotime('-60 days'))],
            exception: false
        ))[0]["count"];

        // Avg usage last 7 days
        $avg_usage_7_days = (float) ($this->database()->find(
            "users",
            ["count(*) as count"],
            ["updated_at >=" => $this->getDate(strtotime('-7 days'))],
            exception: false
        ))[0]["count"];

        // Display global statistics
        $response->getBody()->write(
            json_encode([
                "nb_users" => $nb_users,
                "nb_share" => $nb_share,
                "avg_usage_day" => round(($avg_usage_day / 60), 3),
                "avg_usage_7_days" => round(($avg_usage_7_days / 7), 3)
            ])
        );
        return $response;
    }

    /**
     * Return usage statistics per month.
     * Usage: GET /statistics/avg-usage-month | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getAvgUsageMonth(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Fetch statistics
        $statistics = $this->fetchUsages($request);

        // Fetch all sessions per days for each month
        $raw_data = [];
        foreach ($statistics as $statistic) {
            $full_date = explode('-', (explode(' ', $statistic["updated_at"]))[0]);
            $yyyy_mm   = $full_date[0] . '-' . $full_date[1];

            if (!array_key_exists($yyyy_mm, $raw_data)) {
                for ($i = 0; $i < 31; $i++) {
                    $raw_data[$yyyy_mm][] = 0;
                }
            }

            $dd                           = (int) $full_date[2];
            $raw_data[$yyyy_mm][$dd - 1] += 1;
        }

        // Convert key to integer
        $raw_data    = array_values($raw_data);
        $length_data = count($raw_data);

        // Compute average for one month
        $data = [[], []];
        for ($i = 0; $i < 31; $i++) {
            $data[0][]   = $i + 1;
            $data[1][$i] = $this->average($raw_data, $i, $length_data);
        }

        // Display statistiques information
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    /**
     * Return usage statistics per days.
     * Usage: GET /statistics/avg-usage-day | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getAvgUsageDay(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Fetch statistics
        $statistics = $this->fetchUsages($request);

        // Fetch all sessions per days for each week
        $raw_data = [];
        foreach ($statistics as $statistic) {
            $week = date('Y-w', strtotime($statistic["updated_at"]));
            $day  = date('N', strtotime($statistic["updated_at"]));

            if (!array_key_exists($week, $raw_data)) {
                for ($i = 0; $i < 7; $i++) {
                    $raw_data[$week][] = 0;
                }
            }

            $raw_data[$week][$day - 1] += 1;
        }

        // Convert key to integer
        $raw_data    = array_values($raw_data);
        $length_data = count($raw_data);

        // Compute average for one week
        $data = [
            [
                "Lundi",
                "Mardi",
                "Mercredi",
                "Jeudi",
                "Vendredi",
                "Samedi",
                "Dimanche"
            ],
            []
        ];
        for ($i = 0; $i < 7; $i++) {
            $data[1][$i] = $this->average($raw_data, $i, $length_data);
        }

        // Display statistiques information
        $response->getBody()->write(
            json_encode($data)
        );
        return $response->withStatus(200);
    }

    /**
     * Return usage statistics per hours.
     * Usage: GET /statistics/avg-usage-hour | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getAvgUsageHour(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Fetch statistics
        $statistics = $this->fetchUsages($request);

        // Fetch all sessions per hours for each day
        $raw_data = [];
        foreach ($statistics as $statistic) {
            $date = explode(' ', $statistic["updated_at"]);
            $hour = (int) (explode(':', $date[1]))[0];
            $date = $date[0];

            if (!array_key_exists($date, $raw_data)) {
                for ($i = 0; $i < 24; $i++) {
                    $raw_data[$date][] = 0;
                }
            }

            $raw_data[$date][$hour] += 1;
        }

        // Convert key to integer
        $raw_data    = array_values($raw_data);
        $length_data = count($raw_data);

        // Compute average for one day
        $data = [[], []];
        for ($i = 0; $i < 24; $i++) {
            $data[0][]   = $i;
            $data[1][$i] = $this->average($raw_data, $i, $length_data);
        }

        // Display statistiques information
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    /**
     * Fetch statistics according to date range
     *
     * @param Request $request Slim request interface
     *
     * @return array list of statistics
     * @throws BadRequest if request contain errors
     * @throws NotFound if database return nothing
     */
    private function fetchUsages(Request $request): array
    {
        // Select dates
        $end_date = date("Y-m-d");
        $start_date = explode('-', $end_date);
        $start_date = $start_date[0] . '-' . $start_date[1] . "-01";

        // Fetch statistics
        return $this->database()->find(
            "users",
            ["updated_at"],
            [
                "updated_at >=" => $start_date,
                "updated_at <=" => $end_date
            ],
            order: "updated_at",
            exception: false
        );
    }

    /**
     * Compute average value
     *
     * @param array $data
     * @param int   $i
     * @param int   $lengthData
     *
     * @return float
     */
    private function average(array $data, int $i, int $lengthData): float
    {
        $sum = 0;
        foreach ($data as $value) {
            $sum += $value[$i];
        }

        if ($sum == 0) {
            return 0;
        } else {
            return round($sum / $lengthData, 3);
        }
    }
}