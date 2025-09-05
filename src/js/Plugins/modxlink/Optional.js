import {isNonNullable} from "./Functions";

export default class Optional {
    constructor(tag, value) {
        this.tag = tag;
        this.value = value;
    }
    static some(value) {
        return new Optional(true, value);
    }
    static none() {
        return Optional.singletonNone;
    }
    fold(onNone, onSome) {
        if (this.tag) {
            return onSome(this.value);
        } else {
            return onNone();
        }
    }
    isSome() {
        return this.tag;
    }
    isNone() {
        return !this.tag;
    }
    map(mapper) {
        if (this.tag) {
            return Optional.some(mapper(this.value));
        } else {
            return Optional.none();
        }
    }
    bind(binder) {
        if (this.tag) {
            return binder(this.value);
        } else {
            return Optional.none();
        }
    }
    exists(predicate) {
        return this.tag && predicate(this.value);
    }
    forall(predicate) {
        return !this.tag || predicate(this.value);
    }
    filter(predicate) {
        if (!this.tag || predicate(this.value)) {
            return this;
        } else {
            return Optional.none();
        }
    }
    getOr(replacement) {
        return this.tag ? this.value : replacement;
    }
    or(replacement) {
        return this.tag ? this : replacement;
    }
    getOrThunk(thunk) {
        return this.tag ? this.value : thunk();
    }
    orThunk(thunk) {
        return this.tag ? this : thunk();
    }
    getOrDie(message) {
        if (!this.tag) {
            throw new Error(message !== null && message !== void 0 ? message : 'Called getOrDie on None');
        } else {
            return this.value;
        }
    }
    static from(value) {
        return isNonNullable(value) ? Optional.some(value) : Optional.none();
    }
    getOrNull() {
        return this.tag ? this.value : null;
    }
    getOrUndefined() {
        return this.value;
    }
    each(worker) {
        if (this.tag) {
            worker(this.value);
        }
    }
    toArray() {
        return this.tag ? [this.value] : [];
    }
    toString() {
        return this.tag ? `some(${ this.value })` : 'none()';
    }
}