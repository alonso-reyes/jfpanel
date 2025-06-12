<?php

namespace App\Orchid\Screens;

use App\Models\Post;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PostScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    //public static $model = Post::class;

    public function query(): array
    {
        $posts = Post::paginate(10);
        return [
            'posts' => $posts, // Recuperar datos con paginación
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'PostScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Task')
                ->modal('postModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        // Validate form data, save task to database, etc.
        $request->validate([
            'post.title' => 'required|max:255',
            'post.content' => 'required|max:255',
        ]);

        $post = new Post();
        $post->title = $request->input('post.title');
        $post->content = $request->input('post.content');
        $post->save();
    }

    public function delete(Post $post)
    {
        $post->delete();
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('posts', [
                TD::make('title'),
                TD::make('content'),
                TD::make('Actions')
                ->alignRight()
                ->render(function (Post $post) {
                    return Button::make('Borrar')
                        ->confirm('After deleting, the task will be gone forever.')
                        ->method('delete', ['post' => $post->id]);
                }),
            ]),
    
            Layout::modal('postModal', Layout::rows([
                Input::make('post.title')
                    ->title('Titulo')
                    ->placeholder('Enter title name')
                    ->help('The name of the post to be created.'),
                Input::make('post.content')
                    ->title('Contenido')
                    ->placeholder('Enter content name')
                    ->help('The name of the content post to be created.'),
            ]))
                ->title('Agregar')
                ->applyButton('Guardar'),
        ];
    }
}
