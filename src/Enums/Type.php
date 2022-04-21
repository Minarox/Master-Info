<?php
declare(strict_types = 1);

namespace Enums;

/**
 * Type enum for logs
 */
enum Type {
    case Admin;
    case User;
    case Email;
    case App;
}