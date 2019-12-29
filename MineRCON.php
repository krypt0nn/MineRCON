<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     MineRCON
 * @copyright   2019 Podvirnyy Nikita (KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @author      Podvirnyy Nikita (KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

namespace MineRCON;

class RCON
{
    protected string $ip       = '127.0.0.1';
    protected int $port        = 25575;
    protected string $password = '';

    protected $socket = null;
    
    /**
     * Подключение к RCON сервера
     * 
     * @param string $ip - IP сервера
     * [@param int $port = 25575] - порт RCON
     * [@param string $password = ''] - пароль RCON
     * 
     * @throws \Exception - выбрасывает исключения при ошибках подключения
     */
    public function __construct (string $ip, int $port = 25575, string $password = '')
    {
        $this->ip       = $ip;
        $this->port     = $port;
        $this->password = $password;

        $this->socket = @fsockopen ($this->ip, $this->port, $errno, $errstr);

        if (!$this->socket)
            throw new \Exception ('Socket creation error');

        stream_set_timeout ($this->socket, 5, 0);

        $this->write (5, 3, $this->password);
        $response = $this->read ();

        if ($response['type'] != 2 || $response['id'] != 5)
            throw new \Exception ('Authorization error');
    }

    /**
     * Отправка команды
     * 
     * @param string $command - команда для отправки
     * 
     * @return string|null - возвращает ответ или null в случае неудачи
     */
    public function send (string $command): ?string
    {
        $this->write (6, 2, $command);

        $response = $this->read ();

        return $response['id'] == 6 && $response['type'] == 0 ?
            $response['body'] : null;
    }

    /**
     * Чтение сокета RCON
     * 
     * @return array - возвращает прочитанные данные
     */
    protected function read (): array
    {
        return unpack ('V1id/V1type/a*body',
            fread ($this->socket, unpack ('V1size',
                fread ($this->socket, 4))['size']));
    }

    /**
     * Запись в сокет RCON
     * 
     * @param int $id - ID пакета
     * @param int $type - тип пакета
     * @param string $body - тело пакета
     * 
     * @return RCON - возвращает сам себя
     */
    protected function write (int $id, int $type, string $body): RCON
    {
        $packet = pack ('VV', $id, $type) . $body ."\x00\x00";
        $packet = pack ('V', strlen ($packet)) . $packet;

        fwrite ($this->socket, $packet, strlen ($packet));

        return $this;
    }

    /**
     * Отключение от RCON
     */
    public function disconnect (): void
    {
        if ($this->socket)
            fclose ($this->socket);
    }
}
