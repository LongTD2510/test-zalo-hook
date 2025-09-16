<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class StudentInfoSearchTypeEnum extends Enum
{
    const STUDENT_ID = 'student_id';
    const EXAM_RESULT = 'exam_result';
}
