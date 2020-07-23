export interface IStoreData {
    token: string;
    username: string;
}


class Store {
    private static _store: IStoreData;
    private static isInit = false;
    private static readonly storeKey: string = 'store';
    public static init = () => {
        if (Store.isInit) {
            return;
        }
        Store.isInit = true;
        const str = localStorage.getItem(Store.storeKey);
        let data = {
            token: '',
            username: ''
        };
        if (str) {
            data = JSON.parse(str);
        }
        Store._store = data;
    }

    public static get store(): Readonly<IStoreData> {
        return Store._store;
    }

    public static set store(store: Readonly<IStoreData>) {
        Store._store = store;
        localStorage.setItem(Store.storeKey, JSON.stringify(store));
    }

    public static clear = () => {
        localStorage.clear();
        Store.isInit = false;
        Store.init();
    }
}

Store.init();

export default Store;