import React from 'react';
import {
    Route,
    RouteProps,
    Redirect
} from "react-router-dom";
import {routeConfig} from "./config";
import Store from '../store/Store';

const GuardSign: React.FunctionComponent<RouteProps> = props => {
    return Store.store.token
        ? <Redirect to={routeConfig.names.index}/>
        : <Route {...props}/>
};


export default GuardSign;