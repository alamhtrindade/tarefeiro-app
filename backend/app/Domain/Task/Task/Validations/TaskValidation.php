<?php

namespace App\Domain\Task\Task\Validations;

use App\Application\Enums\SystemMessagesEnum;
use App\Domain\Task\Priority\Enums\PriorityEnum;
use App\Domain\Task\Status\Enums\StatusEnum;
use App\Domain\Task\Task\DTO\TaskDTO;
use App\Domain\Task\Task\Exceptions\TaskException;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class TaskValidation
{
    public function dateIsValid(
        string $startDate,
        string $endDate
    ): void
    {
        $start = Carbon::createFromFormat('Y/m/d', $startDate);
        $end = Carbon::createFromFormat('Y/m/d', $endDate);
        $today = Carbon::today();

        $this->verifyDateIsAfterToday($start, $end, $today);
        $this->verifyEndDateIsBeforeStarDate($start, $end);

    }

    public function normalizeTaskToCreate(
        TaskDTO $taskDTO
    ): TaskDTO
    {
        $taskDTO->priorityId = $taskDTO->priorityId ? $taskDTO->priorityId : PriorityEnum::NO_PRIORITY;
        $taskDTO->statusId   = $taskDTO->statusId ? $taskDTO->statusId : StatusEnum::PENDING;

        return $taskDTO;
    }

    private function verifyDateIsAfterToday(
        Carbon $start,
        Carbon $end,
        Carbon $today
    ): void
    {
        if ($start->isBefore($today) || $end->isBefore($today)) {
            throw new TaskException(
                SystemMessagesEnum::CHOSEN_DATE_BEFORE_TODAY,
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    private function verifyEndDateIsBeforeStarDate(
        Carbon $start,
        Carbon $end
    ): void
    {
        if ($end->isBefore($start)) {
            throw new TaskException(
                SystemMessagesEnum::END_IS_BEFOR_START,
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}