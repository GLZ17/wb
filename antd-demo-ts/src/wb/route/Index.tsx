import React from 'react';
import {
    BrowserRouter as Router,
    Switch
} from "react-router-dom";
import GuardSign from "./GuardSign";
import GuardCommon from "./GuardCommon";
import Login from "../user/sign/Login";
import Register from "../user/sign/Register";
import Password from "../user/sign/Password";
// import Forget from "../user/sign/Forget";
// import Reset from "../user/sign/Reset";
// import Practise from '../practise/Index';
// import Curve from '../curve/index';
import NotFound from "../page/NotFound";
import Home from "../page/Index";


import {routeConfig} from "./config";

const Routes: React.FunctionComponent = () => {
    return (
        <Router>
            <Switch>
                <GuardSign exact={true}
                           path={routeConfig.names.login}
                           component={Login}/>
                <GuardSign exact={true}
                           path={routeConfig.names.register}
                           component={Register}/>
                <GuardSign exact={true}
                           path={routeConfig.names.password}
                           component={Password}/>
                <GuardCommon component={Home}
                             path={routeConfig.names.index}
                             exact={true}/>
                {/*<GuardCommon component={Practise}*/}
                {/*             path={routeConfig.names.practise}*/}
                {/*             exact={true}/>*/}
                {/*<GuardCommon component={Curve}*/}
                {/*             path={routeConfig.names.curve}*/}
                {/*             exact={true}/>*/}
                <GuardCommon component={Home}
                             path={routeConfig.names.index}
                             exact={true}/>
                <GuardCommon component={NotFound}/>
            </Switch>
        </Router>
    );
};


export default Routes;