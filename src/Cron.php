<?php

namespace localzet;

class Cron
{
    /**
     * @var int Уникальный идентификатор задания cron
     */
    protected int $id;

    /**
     * @var array Список всех экземпляров заданий cron
     */
    protected static array $instances = [];

    /**
     * Конструктор Crontab.
     *
     * @param string $rule Правило cron
     * @param callable $callback Функция обратного вызова для выполнения
     * @param string $name Имя задания cron
     */
    public function __construct(protected string $rule, protected $callback, protected string $name = '')
    {
        $this->id = static::createId();
        static::$instances[$this->id] = $this;
        static::tryInit();
    }

    /**
     * Получить правило cron.
     *
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * Получить функцию обратного вызова.
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Получить имя задания cron.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Получить уникальный идентификатор задания cron.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Удалить текущее задание cron.
     *
     * @return bool
     */
    public function destroy(): bool
    {
        return static::remove($this->id);
    }

    /**
     * Получить все задания cron.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return static::$instances;
    }

    /**
     * Удалить задание cron по идентификатору.
     *
     * @param $id
     * @return bool
     */
    public static function remove($id): bool
    {
        $id = $id instanceof Cron ? $id->getId() : $id;
        if (!isset(static::$instances[$id])) {
            return false;
        }
        unset(static::$instances[$id]);
        return true;
    }

    /**
     * Создать уникальный идентификатор для задания cron.
     *
     * @return int
     */
    protected static function createId(): int
    {
        static $id = 0;
        return ++$id;
    }

    /**
     * Попытаться инициализировать задания cron.
     */
    protected static function tryInit()
    {
        static $inited = false;
        if ($inited) {
            return;
        }
        $inited = true;
        $callback = function () use (&$callback) {
            $parser = new CronParser();
            $now = time();

            foreach (static::$instances as $crontab) {
                $rule = $crontab->getRule();
                $cb = $crontab->getCallback();
                if (!$cb || !$rule) {
                    continue;
                }
                $times = $parser->parse($rule);
                foreach ($times as $time) {
                    $t = $time - $now;
                    Timer::add(max(0.000001, $t), $cb, null, false);
                }
            }
            Timer::add(60 - time() % 60, $callback, null, false);
        };

        Timer::add(0.000001, $callback, null, false);
    }
}
