<?php

/**
 * Describes a logger instance
 * See https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 * for the full interface specification.
 */
class logger
{
    private $handlers = [];
    private $file = null;

    /**
     * If filepath is set logs will be printed on it
     */
    function __construct($filepath = null) {
        if ($filepath != null) $this->file = $filepath;
    }

    /**
     * System is unusable.
     */
    public function emergency($message, array $context = array()) {
        $this->log('emergency',$message,$context);
    }

    /**
     * Action must be taken immediately.
     */
    public function alert($message, array $context = array()) {
        $this->log('alert',$message,$context);
    }
    /**
     * Critical conditions.
     */
    public function critical($message, array $context = array()) {
        $this->log('critical',$message,$context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    public function error($message, array $context = array()) {
        $this->log('error',$message,$context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    public function warning($message, array $context = array()) {
        $this->log('warning',$message,$context);
    }

    /**
     * Normal but significant events.
     */
    public function notice($message, array $context = array()) {
        $this->log('notice',$message,$context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     */
    public function info($message, array $context = array()) {
        $this->log('info',$message,$context);
    }

    /**
     * Detailed debug information.
     */
    public function debug($message, array $context = array()) {
        $this->log('debug',$message,$context);
    }

    /**
     * Logs with an arbitrary level.
     */
    public function log($level, $message, array $context = array()) {
        if ($this->file != null) {
          $line = date("Y-m-d H:i:s").','.$level.','.$message."\n";
          file_put_contents($this->file,$line,FILE_APPEND);
        }

        foreach ($this->handlers as $handler) {
            $handler->handle($level, $message, $context = array());
        }
    }

    /**
     * Adds a new handler to the logger.
     */
    public function pushHandler(LogHandler $handler) {
        array_unshift($this->handlers, $handler);
    }
}
