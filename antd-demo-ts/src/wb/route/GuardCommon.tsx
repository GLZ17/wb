import React from 'react';
import {
    Route,
    RouteProps,
    Redirect
} from "react-router-dom";
import {routeConfig} from "./config";
import Store from '../store/Store';
import Menu from "../common/Menu";
import Logout from "../user/sign/Logout";

class GuardCommon extends React.Component<RouteProps>{
    public render() {
        return Store.store.token
            ? (<React.Fragment>
                <Menu/>
                <Logout/>
                <Route {...this.props}/>
            </React.Fragment>)
            : <Redirect to={routeConfig.names.login}/>
    }
}


export default GuardCommon;