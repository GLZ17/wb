import React from 'react';
import Store from "../../store/Store";
import {apiRequest} from "../../api/request";
import {withRouter, RouteComponentProps} from 'react-router-dom';
import Message from "../../common/Message";
import Loading from "../../common/Loading";
import {routeConfig} from "../../route/config";

class Logout extends React.Component<RouteComponentProps, {}> {
    private handleOutClick = async () => {
        Loading.start();
        const res = await apiRequest.sign.logout();
        Loading.stop();
        Message.message(res);
        if (!res.code) {
            Store.clear();
            this.props.history.push(routeConfig.names.login);
        }
    }

    public render() {
        return (
            <div className={'lt-page pf t0 r0'}>
                <span>欢迎 {Store.store.username}</span>
                <span onClick={this.handleOutClick}
                      className={'lt-out'}>退出</span>
            </div>
        );
    }
}

export default withRouter(Logout);