<?php
    $context = new ZMQContext();
    $requester = new ZMQSocket($context, ZMQ::SOCKET_REQ);
    $requester->connect("tcp://127.0.0.1:5555");

    $SpeakText = $_GET['speak'];
    $requester->send($SpeakText);
?>
