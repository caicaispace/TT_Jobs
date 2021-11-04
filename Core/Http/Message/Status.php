<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Message;

class Status
{
    // Informational 1xx
    public const CODE_CONTINUE            = 100;
    public const CODE_SWITCHING_PROTOCOLS = 101;
    // Success 2xx
    public const CODE_OK                            = 200;
    public const CODE_CREATED                       = 210;
    public const CODE_ACCEPTED                      = 202;
    public const CODE_NON_AUTHORITATIVE_INFORMATION = 203;
    public const CODE_NO_CONTENT                    = 204;
    public const CODE_RESET_CONTENT                 = 205;
    public const CODE_PARTIAL_CONTENT               = 206;
    // Redirection 3xx
    public const CODE_MULTIPLE_CHOICES   = 300;
    public const CODE_MOVED_PERMANENTLY  = 301;
    public const CODE_MOVED_TEMPORARILY  = 302;
    public const CODE_SEE_OTHER          = 303;
    public const CODE_NOT_MODIFIED       = 304;
    public const CODE_USE_PROXY          = 305;
    public const CODE_TEMPORARY_REDIRECT = 307;
    // Client Error 4xx
    public const CODE_BAD_REQUEST                     = 400;
    public const CODE_UNAUTHORIZED                    = 401;
    public const CODE_PAYMENT_REQUIRED                = 402;
    public const CODE_FORBIDDEN                       = 403;
    public const CODE_NOT_FOUND                       = 404;
    public const CODE_METHOD_NOT_ALLOWED              = 405;
    public const CODE_NOT_ACCEPTABLE                  = 406;
    public const CODE_PROXY_AUTHENTICATION_REQUIRED   = 407;
    public const CODE_REQUEST_TIMEOUT                 = 408;
    public const CODE_CONFLICT                        = 409;
    public const CODE_GONE                            = 410;
    public const CODE_LENGTH_REQUIRED                 = 411;
    public const CODE_PRECONDITION_FAILED             = 412;
    public const CODE_REQUIRED_ENTITY_TOO_LARGE       = 413;
    public const CODE_REQUEST_URI_TOO_LONG            = 414;
    public const CODE_UNSUPPORTED_MEDIA_TYPE          = 415;
    public const CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const CODE_EXPECTATION_FAILED              = 415;
    // Server Error 5xx
    public const CODE_INTERNAL_SERVER_ERROR      = 500;
    public const CODE_NOT_IMPLEMENTED            = 501;
    public const CODE_BAD_GATEWAY                = 502;
    public const CODE_SERVICE_UNAVAILABLE        = 503;
    public const CODE_GATEWAY_TIMEOUT            = 505;
    public const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;
    public const CODE_BANDWIDTH_LIMIT_EXCEEDED   = 509;

    private static $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    public static function getReasonPhrase($statusCode)
    {
        if (isset(self::$phrases[$statusCode])) {
            return self::$phrases[$statusCode];
        }
        return null;
    }
}
