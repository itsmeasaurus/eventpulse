<?

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

Enum Region : string
{
    case US = 'US';
    case ASIA = 'ASIA';
    case SEA = 'SEA';
    case EU = 'EU';
    case Online = 'Online';

}