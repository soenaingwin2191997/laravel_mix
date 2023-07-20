<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;

class WishlistController extends Controller {
    public function wishlist() {
        $pageTitle = 'My Wishlists';
        $wishlists = Wishlist::where('user_id', auth()->id())->with('item', 'episode.item')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.wishlist.index', compact('pageTitle', 'wishlists'));
    }

    public function wishlistRemove($id) {
        Wishlist::where('user_id', auth()->id())->where('id', $id)->delete();
        $notify[] = ['success', 'Item removed from your wishlists'];
        return back()->withNotify($notify);
    }
}
