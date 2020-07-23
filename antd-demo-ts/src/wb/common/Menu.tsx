import '../page/menu.css';
import React from 'react';
import {
    NavLink,
    RouteComponentProps as Props,
    withRouter
} from 'react-router-dom';
import {routeConfig} from "../route/config";

const MenuComponent: React.FunctionComponent<Props> = props => {
    const url = props.location.pathname;
    return (
        <ul className={'menu tac pf t0 l0 b0'}>
            {routeConfig.paths.map(p => (
                <li key={p.id}>
                    <NavLink to={p.path}
                             activeClassName={'link-active'}
                             className={`link ${url.startsWith(p.name) ? 'link-active' : ''}`}>
                        {p.name}
                    </NavLink>
                </li>
            ))}
        </ul>
    );
};

const Menu = withRouter(MenuComponent);
export default Menu;