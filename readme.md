## Важно

Менаджер работает с драйвером очередей AMQP поэтому кроме "yii2/queue", 
который есть в зависимостях этого пакета, необходимо так же установить
транспорт. 

Я рекомендую "enqueue/amqp-lib"

## Установка
Установка через composer:

    composer require brezgalov/yii2-amq-workers-manager --prefer-dist

Применяем миграции

    php yii migrate --migrationPath="vendor/brezgalov/yii2-amq-workers-manager/migrations"

Подключаем компонент в приложении

    $config = [
      'bootstrap' => [
        'workersManager',
        'queue',
      ],
      'components' => [
        'workersManager' => WorkersManagerService::class,
        'queue' => [
          'class' => \yii\queue\amqp_interop\Queue::class,
          'host' => '127.0.0.1',
          'port' => 5672,
          'user' => 'guest',
          'password' => 'guest',
          'queueName' => 'test',
        ],
        ...

## Применение

В базе данных в таблице workers_configs создаем запись

    <queue_component_name: 'queue', workers_count: 1>

Запускаем обработку воркеров через консоль

    php yii workers/manage workersManager

Наблюдаем что в таблице workers_statuses появился воркер.

Далее, подключаем команду "workers/manage" в крон/шедулер на интервал подходящий нам

Для того чтобы статус воркеров менялся - периодически опрашиваем их через крон/шедулер командой

    php yii workers/check

Все! Теперь при изменении количества воркеров в бд, менаджер автоматически остановит/запустит воркеры

## Выполнение задач

При создании задач можно использовать класс AbstractJob. 
Он позволяет задаче принятой на выполнение ставить отметку is_busy в статус воркера. 
Это позволяет менаджеру видеть воркеры, которые сейчас в работе и не останавливать их, 
даже если такая необходимость есть (остановит когда воркер будет is_busy = 0)

Например:

    class TestPlaceholderJob extends AbstractJob implements JobInterface
    {
        /**
        * @var int
        */
        public $sleep = 10;
    
        /**
         * @param \yii\queue\Queue $queue
         * @return mixed|void
         */
        public function run($queue)
        {
            sleep($this->sleep);
        }
    }


