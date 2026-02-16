<?php


class Core_Controllers_Front{
    public function run(){
        $request = Sdp::getModel("core/request");

        $class = sprintf("%s_Controllers_%s",
            ucfirst($request->getModuleName()),
            ucfirst($request->getControllersName())
            );

        $actionName = $request->getActionName() ."Action";

        $className = new $class();

        $className->$actionName();


    }
}