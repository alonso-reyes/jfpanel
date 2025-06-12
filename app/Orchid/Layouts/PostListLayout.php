<?php

namespace App\Orchid\Layouts;

use App\Models\Post;
use Illuminate\Http\Request;
//use Orchid\Alert\Alert;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Support\Facades\Alert;

class PostListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'posts';

    public function actions(): array
{
    return [
        'delete',
    ];
}

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('title', 'Title')
                ->render(function (Post $post) {
                    return Link::make($post->title)
                        ->route('platform.post.edit', $post);
                }),

            TD::make('created_at', 'Created'),
            TD::make('updated_at', 'Last edit'),

            // TD::make('')
            // ->alignRight()
            // ->render(function (Post $post) {
            //     return Button::make('')
            //         ->icon('pencil')
            //         ->confirm('After deleting, the task will be gone forever.')
            //         ->method('delete', ['post' => $post->id]);
            // }),
            
            TD::make('')
                ->alignRight()
                ->render(function (Post $post) {
                return
                    '<div style="display: inline-flex; gap: 5px;">' .
                        Link::make('')
                        ->icon('pencil')
                        ->route('platform.post.edit', $post)
                        ->render() .
                        Button::make('')
                            ->icon('trash')
                            ->confirm('After deleting, the post will be gone forever.')
                            ->method('delete', ['post' => $post->id])
                             .
                    '</div>';
            }),

        ];
    }

}
