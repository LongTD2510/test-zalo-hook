<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FileType extends Enum
{
    const CATEGORY = 'category';
    const POST = 'post';
    const HOME_PAGE = 'home-page';
    const TEACHER = 'teacher';
    const STUDENT = 'student';
    const TEMPLATE = 'template';
}
