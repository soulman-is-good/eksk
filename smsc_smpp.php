<?php
/**
 * Class for handling smpp connection 
 * and sending sms throught a socket
 */
class SMPP {

    private $socket;
    private $sequence_number = 1;
    
    public $host = null;
    public $port = null;
    public $login = null;
    public $password = null;
    public $charset = 'UTF-8';

    public function __construct($config) {
        foreach($config as $key=>$val){
            if(property_exists($this, $key))
                $this->$key = $val;
        }
        if($this->host === null || $this->port === null || $this->login === null || $this->password === null)
            throw new Exception ('Settings not enough!');
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$this->socket || !socket_connect($this->socket, $this->host, $this->port))
            throw new Exception(socket_strerror(socket_last_error()));

        if (!$this->bind())
            throw new Exception("Bind error");
    }

    public function __destruct() {
        if ($this->socket) {
            $this->unbind();
            socket_close($this->socket);
        }
    }

    private function send_pdu($pdu) {
        $length = strlen($pdu);

        if ($this->socket && socket_write($this->socket, $pdu, $length) == $length) {
            $reply = unpack("N4", $this->read_pdu());
            return $reply[4] == $this->sequence_number++ && $reply[3] == 0; // ok
        }

        return false;
    }

    private function read_pdu() {
        $pdu = "";
        $wait_sec = 4;

        while (socket_recv($this->socket, $pdu, 16, MSG_WAITALL) != 16 && --$wait_sec >= 0)
            sleep(1);

        if ($wait_sec >= 0) {
            $header = unpack("N4", $pdu);
            $pdu .= socket_read($this->socket, $header[1] - 16); // body
        }

        return $pdu;
    }

    private function bind() {
        $pdu = pack("a" . strlen($this->login) . "xa" . strlen($this->password) . "xxCCCx", $this->login, $this->password, 0x34, 5, 1); // body
        $pdu = pack("NNNN", strlen($pdu) + 16, 0x02/* BIND_TRANSMITTER */, 0, $this->sequence_number) . $pdu; // header + body

        return $this->send_pdu($pdu);
    }

    public function unbind() {
        $pdu = pack("NNNN", 16, 0x06/* UNBIND */, 0, $this->sequence_number);
        return $this->send_pdu($pdu);
    }
    
    /**
     * SMS send function
     * 
     * @param string $phone phone number list, divided by comma or semicolon.
     * @param string $message Message text
     * @param string $sender sender name (Sender ID). To turn off default Sender ID you need to pass empty string or a dot "."
     * @return string socket response result
     */
    public function send_sms($phone, $message, $sender = ".") {
        if (preg_match('/[`\x80-\xff]/', $message)) {
            $message = iconv($this->charset, "UTF-16BE", $message);
            $coding = 2; // ucs2
        }
        else
            $coding = 1; // 8bit

        $sm_length = strlen($message);

        $pdu = pack("xCCa" . strlen($sender) . "xCCa" . strlen($phone) . "xCCCa1a1CCCCCnna" . $sm_length, // body
                5, // source_addr_ton
                1, // source_addr_npi
                $sender, // source_addr
                1, // dest_addr_ton
                1, // dest_addr_npi
                $phone, // destination_addr
                0, // esm_class
                0, // protocol_id
                1, // priority_flag
                "", // schedule_delivery_time
                "", // validity_period
                0, // registered_delivery_flag
                0, // replace_if_present_flag
                $coding * 4, // data_coding
                0, // sm_default_msg_id
                0, // sm_length + short_message [empty]
                0x0424, // TLV message_payload tag
                $sm_length, // message length
                $message // message
        );

        $pdu = pack("NNNN", strlen($pdu) + 16, 0x04/* SUBMIT_SM */, 0, $this->sequence_number) . $pdu; // header + body

        return $this->send_pdu($pdu);
    }

}
